<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CortexCRM Register</title>
    <link rel="shortcut icon" href="{{asset('img/mylogo.png')}}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: #e9ecef;
      display: flex;
      justify-content: center;
      align-items: center;
      height: auto;
      margin: 50px;
    }
    .auth-container {
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      display: flex;
      overflow: hidden;
      max-width: 1200px;
      width: 100%;
      margin-top:5%;
    }
    .left-panel {
      background: #3b65ea;
      color: white;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 40px;
    }
    .left-panel h2 {
      font-weight: 700;
    }
    .right-panel {
      flex: 1;
      padding: 40px;
    }
    .form-control::placeholder {
      color: #aaa;
    }
    .social-icons i {
      font-size: 20px;
      margin: 0 10px;
      cursor: pointer;
      color: #333;
    }
    .tab-pane input {
      margin-bottom: 15px;
    }
    input{
    background-color: #eceef0 !important;
    height: 55px !important;
    }

    textarea{
      background-color: #eceef0 !important;
    }

    .hidden { 
      display: none;
    }

    @media screen and (max-width: 600px) {
       .left-panel{
            display:none;
       }
    }



  </style>
</head>
<body>

<div class="auth-container">

<!-- Right panel (Login) -->
<div class="right-panel">
    <h3 class="mb-4">Signup</h3>
    <form>
        <ul class="nav nav-tabs mb-3" id="registerTabs" role="tablist">
            <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#companyTab" type="button">Company Details</button>
            </li>
            <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#userTab" type="button">User Info</button>
            </li>
        </ul>
     
        <div class="tab-content">
            <!-- Company Tab -->
            <div class="tab-pane fade show active" id="companyTab">
            <form id="companyForm">
                <div class="row">
                  <div class="col-sm-6">
                      <input type="text" name="companyName" id="companyName" class="form-control mb-4" placeholder="Company Name" />
                  </div>
                  <div class="col-sm-6">
                      <input type="email" name="companyEmail" id="companyEmail" class="form-control mb-4" placeholder="Company Email" />
                  </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <input type="text" name="companyAddress"  id="companyAddress"  class="form-control mb-4" placeholder="Company Address" />
                    </div>
                    <div class="col-sm-6">
                        <input type="text" name="country" id="country" class="form-control mb-4" placeholder="Country" />
                    </div>
                </div>
                <textarea name="companyDescription" class="form-control mb-4" id="companyDescription" placeholder="Company Description"></textarea>
               
                <div>
                  <h4>Select Business Type:</h4>
                  <label class="mb-2">
                    <input type="radio"  style="height: 16px !important;" class="form-check-input mb-2" name="business_type" value="service"> Service
                  </label>
                  <label style="margin-left: 40px;">
                    <input type="radio"  style="height: 16px !important;" class="form-check-input mb-2"  name="business_type" value="product"> Product
                  </label>
                </div>

                 <div id="serviceBox" class="textarea-box hidden">
                    <label>Service Knowledge:</label><br>
                    <textarea rows="3" cols="40" name="serviceKnowledge" id="serviceKnowledge" class="form-control mb-4 mt-2"></textarea>
                  </div>

                  <div id="productBox" class="textarea-box hidden">
                    <label>Product Knowledge:</label><br>
                    <textarea rows="3" cols="40" name="productKnowledge" id="productKnowledge" class="form-control mb-4 mt-2"></textarea>
                  </div>

                   <div>
                    <label>Pricing Guidelines:</label><br>
                    <textarea rows="3" cols="40" name="priceGuidelines"  id="priceGuidelines" class="form-control mb-4 mt-2"></textarea>
                  </div>

                <button type="button" id="companyBtn" style="background-color: #3b65ea;color: white;width: 100px;height: 50px;outline: none;border: none;border-radius: 10px;">Next  <i class='fas fa-arrow-alt-circle-right'></i></button>
            </form>
            </div>

            <!-- User Tab -->
            <div class="tab-pane fade" id="userTab">
            <form id="userForm">
                <input type="text" name="firstname"  id="firstname"  class="form-control" placeholder="First Name" />
                <input type="text" name="lastname" id="lastname"  class="form-control" placeholder="Last Name" />
                <input type="email" name="userEmail" id="userEmail" class="form-control" placeholder="User Email" />
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" />
                <input type="password" name="confirmpassword" id="confirmpassword" class="form-control" placeholder="Confirm Password" />
                <button type="button" id="userBtn" style="background-color: #3b65ea;color: white;width: 100px;height: 50px;outline: none;border: none;border-radius: 10px;">Register </button>
                <div class="showalert mt-3"></div>
            </form>
            </div>
        </div>

    </form>
  </div>

  <!-- Left panel -->
  <div class="left-panel text-center">
    <img src="{{asset('img/mylogo.png')}}" alt="logo" width="100px" height="100px" style="border-radius: 50px;margin-bottom: 30px;">
    <h1 style="font-family: sans-serif;">Hello, Welcome!</h1>
    <p class="mt-3">Already have an account?</p>
    <button onClick="window.location='{{route('login')}}'" style="width: 45%;height: 9%;border-radius: 12px;" class="btn btn-outline-light mt-2" data-bs-toggle="modal" data-bs-target="#registerModal">Login</button>
  </div>

  
</div>


<!-- Bootstrap & Icons -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#companyBtn').click(function(){
        $('[data-bs-target="#userTab"]').trigger('click');
    })

    $(document).on('click','#userBtn',function(){

      let companyName= $('#companyName').val();
      let companyEmail= $('#companyEmail').val();
      let companyAddress= $('#companyAddress').val();
      let country= $('#country').val();
      let firstname= $('#firstname').val();
      let lastname= $('#lastname').val();
      let userEmail= $('#userEmail').val();
      let password= $('#password').val();
      let confirmpassword= $('#confirmpassword').val();
      let companyDescription= $('#companyDescription').val();
      let businessType = $("input[name='business_type']:checked").val();
      let service_knowledge= $('#serviceKnowledge').val();
      let product_knowledge= $('#productKnowledge').val();
      let priceGuidelines= $('#priceGuidelines').val();
        
       $.ajax({
        url:'{{route('register')}}',
        type:"post",
        data:{
            companyName:companyName,
            company_email:companyEmail,
            companyAddress:companyAddress,
            country:country,
            firstname:firstname,
            lastname:lastname,
            email:userEmail,
            password:password,
            password_confirmation:confirmpassword,
            companyDescription:companyDescription,
            businessType:businessType,
            service_knowledge:service_knowledge,
            product_knowledge:product_knowledge,
            priceGuidelines:priceGuidelines,
            csrf: $('meta[name="csrf-token"]').attr('content')
        }
       }).done(function(response){

        $('.showalert').html("<h6 class='text-primary'>User Created Successfully</h6>");
        window.location="{{route('dashboard')}}";

       }).fail(function(error){

        $('.showalert').html("<h6 class='text-danger'>"+error.responseJSON.message+"</h6>")
       })


    })


    document.querySelectorAll('input[name="business_type"]').forEach((radio) => {
      radio.addEventListener('change', function () {
        if (this.value === "service") {
          document.getElementById("serviceBox").classList.remove("hidden");
          document.getElementById("productBox").classList.add("hidden");
        } else if (this.value === "product") {
          document.getElementById("productBox").classList.remove("hidden");
          document.getElementById("serviceBox").classList.add("hidden");
        }
      });
    });


})

</script>
</body>
</html>
