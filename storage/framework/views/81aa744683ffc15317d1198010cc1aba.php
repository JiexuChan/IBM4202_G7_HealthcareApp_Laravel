<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo e(asset('css/animation.css')); ?>">  
    <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">  
    <link rel="stylesheet" href="<?php echo e(asset('css/index.css')); ?>">
    <title>eDoc</title>
    <style>
        table{
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
        
</head>
<body>
    
    <div class="full-height">
        <center>
        <table border="0">
            <tr>
                <td width="80%">
                    <font class="edoc-logo">eDoctors. </font>
                    <font class="edoc-logo-sub">| IBM4202 Group Final Assessment</font>
                </td>
                <td width="10%">
                    <a href="<?php echo e(url('/login')); ?>"  class="non-style-link"><p class="nav-item">LOGIN</p></a>
                </td>
                <td  width="10%">
                    <a href="<?php echo e(url('/signup')); ?>" class="non-style-link"><p class="nav-item" style="padding-right: 10px;">REGISTER</p></a>
                </td>
            </tr>
            
            <tr>
                <td  colspan="3">
                    <p class="heading-text">Your Health, Your Schedule — Simplified.</p>

                </td>
            </tr>
            <tr>
                <td  colspan="3">
                    <p class="sub-text2">Feeling unwell? Skip the long queues and manage your medical visits online. <br>
                         Search, compare, and book appointments with verified doctors in just a few clicks.  <br>
                     Stay connected with your healthcare providers — fast, secure, and convenient.</p>
                </td>
            </tr>
            <tr>
                
                <td colspan="3">
                    <center>
                    <a href="<?php echo e(url('/login')); ?>" >
                        <input type="button" value="Make Appointment" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                    </a>
                </center>
                </td>
                
            </tr>
            <tr>
                <td colspan="3">
                   
                </td>
            </tr>
        </table>
        <p class="sub-text2 footer-hashen">A Website Simulation by Chan/Hew/Joseph/Pang/Tan.</p>
    </center>
    
    </div>
</body>
</html><?php /**PATH C:\xampp\htdocs\HealthcareApp_Laravel\resources\views/home.blade.php ENDPATH**/ ?>