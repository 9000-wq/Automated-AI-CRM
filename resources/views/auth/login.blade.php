<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CortexCRM Login</title>
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
      height: 100vh;
    }
    .auth-container {
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
      display: flex;
      overflow: hidden;
      max-width: 900px;
      width: 100%;
      
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
<div class="right-panel mt-4">
    <h2 class="mb-5 text-center">Login</h2>
                 <!-- User Tab -->
            <div id="userTab">
            <form id="userForm">
                <input type="email" name="userEmail" id="userEmail" class="form-control mb-4 " placeholder="User Email" />
                <input type="password" name="password" id="password" class="form-control mb-4" placeholder="Password" />
                <button type="button" id="loginBtn" style="background-color: #3b65ea;color: white;width: 100px;height: 50px;outline: none;border: none;border-radius: 10px;">Login </button>
                <div class="showalert mt-3"></div>
            </form>
            </div>

  </div>

  <!-- Left panel -->
  <div class="left-panel text-center">
    <img src="{{asset('img/mylogo.png')}}" alt="logo" width="100px" height="100px" style="border-radius: 50px;margin-bottom: 30px;">
    <h1 style="font-family: sans-serif;">Hello, Welcome!</h1>
    <p class="mt-3">Don't have an account?</p>
    <button  onClick="window.location='{{route('register')}}'" style="width: 45%;height: 15%;border-radius: 12px;" class="btn btn-outline-light mt-2" data-bs-toggle="modal" data-bs-target="#registerModal">Signup</button>
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

   

    $(document).on('click','#loginBtn',function(){

   
       let userEmail= $('#userEmail').val();
       let password= $('#password').val();
        
       $.ajax({
        url:'{{route('userlogin')}}',
        type:"post",
        data:{
            email:userEmail,
            password:password,
            csrf: $('meta[name="csrf-token"]').attr('content')
        }
       }).done(function(response){

        $('.showalert').html("<h6 class='text-primary'>User Logged In Successfully</h6>");
        window.location="{{route('dashboard')}}";

       }).fail(function(error){

        $('.showalert').html("<h6 class='text-danger'>"+error.responseJSON.message+"</h6>")
       })


    })

})

</script>
</body>
</html>
