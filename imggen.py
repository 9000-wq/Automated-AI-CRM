import os
import json
import base64
import shutil
import uuid
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from openai import OpenAI
from dotenv import load_dotenv
from ad_store import get_ad, save_ad

# Load API key
load_dotenv()
client = OpenAI(api_key="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")

DATA_DIR = "ads_data"
os.makedirs(DATA_DIR, exist_ok=True)

app = FastAPI(title="Post Generator API")

# ---------------------------
# Request Models
# ---------------------------
class GenerateRequest(BaseModel):
    product_info: str

class ReviewRequest(BaseModel):
    uid: str
    feedback: str = ""
    regenerate: bool = False

# ---------------------------
# Utility
# ---------------------------
def save_to_folder(ad: dict):
    """Save all images (versions) + log to folder named after uid"""
    uid = ad["uid"]
    folder = os.path.join(DATA_DIR, uid)
    os.makedirs(folder, exist_ok=True)

    # Save all image versions
    for v in ad.get("versions", []):
        img_path = os.path.join(folder, f"image_v{v['version']}.png")
        with open(img_path, "wb") as f:
            f.write(base64.b64decode(v["image"].split(",")[1]))

    # Save log
    log_path = os.path.join(folder, "log.json")
    with open(log_path, "w") as f:
        json.dump(ad, f, indent=2)

def delete_folder(uid: str):
    folder = os.path.join(DATA_DIR, uid)
    if os.path.exists(folder):
        shutil.rmtree(folder)

# ---------------------------
# /generate Route
# ---------------------------
@app.post("/generate")
def generate_for_frontend(request: GenerateRequest):
    try:
        prompt = f"""
        You are an expert social media assistant. Based on the product info, 
        return a JSON with:
        - caption: engaging caption (2–3 sentences)
        - hashtags: list of 5–7 relevant hashtags
        - image_prompt: a detailed DALL-E prompt

        Product Info: "{request.product_info}"
        """
        response = client.chat.completions.create(
            model="gpt-4o-mini",
            messages=[{"role": "user", "content": prompt}],
            response_format={"type": "json_object"},
        )

        content_json = json.loads(response.choices[0].message.content)

        # Generate first image
        image_result = client.images.generate(
            model="dall-e-3",
            prompt=content_json["image_prompt"],
            size="1024x1024",
            response_format="b64_json"
        )

        image_base64 = image_result.data[0].b64_json
        image_data_uri = f"data:image/png;base64,{image_base64}"

        ad_data = {
            "uid": str(uuid.uuid4()),
            "caption": content_json["caption"],
            "hashtags": content_json["hashtags"],
            "prompt": content_json["image_prompt"],
            "versions": [{"version": 1, "image": image_data_uri}]
        }

        saved = save_ad(ad_data)
        save_to_folder(saved)
        return {"success": True, "data": saved}

    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Generation failed: {e}")

# ---------------------------
# /review-ad Route
# ---------------------------
@app.post("/review-ad")
def review_ad(request: ReviewRequest):
    ad = get_ad(request.uid)
    if not ad:
        raise HTTPException(status_code=404, detail="Ad not found.")

    feedback_text = request.feedback or ""
    version_to_use = None

    # -------------------
    # 1️⃣ Strict GPT-based version parsing
    # -------------------
    if feedback_text.strip():
        try:
            current_versions = [v['version'] for v in ad.get('versions', [])]

            # Correct previous version logic
            if len(current_versions) >= 2:
                previous_version = current_versions[-2]  # second-to-last
            else:
                previous_version = current_versions[0]   # only one version exists

            analysis_prompt = f"""
You are an assistant that ONLY returns a single number representing the image version the user wants. 
Rules:
- If the user says "first", return 1.
- If the user says "previous" or "last pic before current", return {previous_version}.
- If the user says "latest" or "last" or "most recent", return {current_versions[-1]}.
- If the user specifies "vN" (like v2 or v3), return N.
- If the user mentions ordinals like "second" or "third", map them to version numbers: first=1, second=2, third=3.
- ONLY return a number. Do NOT return any text, punctuation, or explanation.
- If you cannot determine a version, return 0.

Available versions: {', '.join(map(str, current_versions))}

User feedback: "{feedback_text}"
"""
            analysis_resp = client.chat.completions.create(
                model="gpt-4o-mini",
                messages=[{"role": "user", "content": analysis_prompt}],
                max_tokens=5
            )
            result = analysis_resp.choices[0].message.content.strip()
            if result.isdigit() and int(result) in current_versions:
                version_to_use = int(result)
        except Exception:
            pass  # fallback if GPT fails

    # -------------------
    # 2️⃣ Return requested version if determined
    # -------------------
    if version_to_use:
        selected_version = next((v for v in ad.get("versions", []) if v["version"] == version_to_use), None)
        if selected_version:
            return {"message": f"Returning version {version_to_use}", "image": selected_version["image"]}
        else:
            raise HTTPException(status_code=404, detail=f"Version {version_to_use} not found.")

    # -------------------
    # 3️⃣ Regenerate image if requested
    # -------------------
    if request.regenerate:
        last_version_number = ad.get("versions", [])[-1]["version"] if ad.get("versions") else 0
        new_version_number = last_version_number + 1
        new_prompt = f"{ad['prompt']} — Adjusted based on feedback: {feedback_text}"

        try:
            img = client.images.generate(
                model="dall-e-3",
                prompt=new_prompt,
                size="1024x1024",
                response_format="b64_json"
            )
            image_base64 = img.data[0].b64_json
            new_image_data_uri = f"data:image/png;base64,{image_base64}"
        except Exception as e:
            raise HTTPException(status_code=500, detail=f"Image regeneration failed: {e}")

        ad.setdefault("versions", []).append({"version": new_version_number, "image": new_image_data_uri})
        ad["prompt"] = new_prompt
        ad["review_feedback"] = feedback_text

        saved = save_ad(ad)
        save_to_folder(saved)
        return {"message": f"Review complete, saved as v{new_version_number}", "ad": saved}

    # -------------------
    # 4️⃣ No regeneration or retrieval
    # -------------------
    return {"message": "No regeneration requested", "ad": ad}


# ---------------------------
# /end-chat Route
# ---------------------------
@app.delete("/end-chat/{uid}")
def end_chat(uid: str):
    delete_folder(uid)
    return {"message": f"Chat {uid} ended and data deleted."}



from fastapi import FastAPI, APIRouter, HTTPException
from pydantic import BaseModel
from ad_store import get_last_ad, save_ad
from openai import OpenAI
import uuid
from typing import List, Optional
import requests
import base64
import tempfile
import os
# ====== MODELS ======
class FacebookPostRequest(BaseModel):
    image: str                    # Required (URL or base64 string)
    caption: str                  # Required caption
    hashtags: Optional[List[str]] = None
    page_id: str
    page_access_token: str        # Must be a PAGE access token


# ====== FACEBOOK POST HELPER ======
def post_to_facebook_page(req: FacebookPostRequest):
    """
    Post an image to a Facebook Page using Graph API v23.0.
    Supports both image URLs and base64-encoded images.
    """
    message = req.caption
    if req.hashtags:
        message += " " + " ".join(req.hashtags)

    url = f"https://graph.facebook.com/v23.0/{req.page_id}/photos"

    # Case 1: Direct image URL
    if req.image.startswith("http"):
        data = {
            "url": req.image,
            "caption": message,
            "access_token": req.page_access_token,
            "published": "true"
        }
        response = requests.post(url, data=data)

    # Case 2: Base64-encoded image
    else:
        try:
            image_data = base64.b64decode(req.image)
        except Exception:
            raise HTTPException(status_code=400, detail="Invalid base64 image string")

        with tempfile.NamedTemporaryFile(delete=False, suffix=".jpg") as tmp_file:
            tmp_file.write(image_data)
            tmp_file.flush()
            tmp_file_name = tmp_file.name

        with open(tmp_file_name, "rb") as f:
            files = {"source": f}
            data = {
                "caption": message,
                "access_token": req.page_access_token,
                "published": "true"
            }
            response = requests.post(url, data=data, files=files)

        os.remove(tmp_file_name)

    # Handle errors
    if response.status_code not in [200, 201]:
        try:
            error_detail = response.json()
        except Exception:
            error_detail = response.text
        raise HTTPException(
            status_code=response.status_code,
            detail={
                "error": "Facebook API error",
                "detail": error_detail
            }
        )

    return response.json()


# ====== ROUTES ======
from fastapi import File, UploadFile, Form

@app.post("/facebook/post")
async def post_to_facebook(
    picture: UploadFile = File(...),
    caption: str = Form(...),
    hashtags: Optional[str] = Form(None),
    page_id: str = Form(...),
    page_access_token: str = Form(...)
):
    
    content = await picture.read()
    encoded_image = base64.b64encode(content).decode("utf-8")

    req = FacebookPostRequest(
    image=encoded_image,   # base64 string
    caption=caption,
    hashtags=hashtags.split(" ") if hashtags else [],
    page_id=page_id,
    page_access_token=page_access_token,
)
   


    fb_response = post_to_facebook_page(req)
    return {"status": "success", "facebook_response": fb_response}

