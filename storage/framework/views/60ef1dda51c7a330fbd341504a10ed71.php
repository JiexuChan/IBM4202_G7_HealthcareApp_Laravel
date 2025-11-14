<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo e(asset('css/animations.css')); ?>">  
    <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">  
    <link rel="stylesheet" href="<?php echo e(asset('css/admin.css')); ?>">
    <title>Appointments</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px" >
                                    <img src="<?php echo e(asset('img/user.png')); ?>" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo e($username); ?></p>
                                    <p class="profile-subtitle"><?php echo e($useremail); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="<?php echo e(url('/logout')); ?>" ><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                    </table>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-dashbord" >
                        <a href="<?php echo e(url('/admin/dashboard')); ?>" class="non-style-link-menu"><div><p class="menu-text">Dashboard</p></a></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-doctor ">
                        <a href="<?php echo e(url('/admin/doctors')); ?>" class="non-style-link-menu "><div><p class="menu-text">Doctors</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-schedule ">
                        <a href="<?php echo e(url('/admin/schedule')); ?>" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment menu-active menu-icon-appoinment-active">
                        <a href="<?php echo e(url('/admin/appointments')); ?>" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Appointment</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient">
                        <a href="<?php echo e(url('/admin/patients')); ?>" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%" >
                        <a href="<?php echo e(url('/admin/appointments')); ?>" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Appointment Manager</p>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php echo e($today); ?>

                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="<?php echo e(asset('img/calendar.svg')); ?>" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Appointments (<?php echo e($appointment_count); ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;" >
                        <center>
                        <table class="filter-container" border="0" >
                        <tr>
                           <td width="10%"></td> 
                           <td width="5%" style="text-align: center;">Date:</td>
                           <td width="30%">
                           <form action="<?php echo e(url('/admin/appointments')); ?>" method="get">
                                <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">
                           </td>
                           <td width="5%" style="text-align: center;">Doctor:</td>
                           <td width="30%">
                                <select name="docid" id="" class="box filter-container-items" style="width:90% ;height: 37px;margin: 0;" >
                                    <option value="" disabled selected hidden>Choose Doctor Name from the list</option><br/>
                                    <?php $__currentLoopData = $doctor_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($doctor->docid); ?>"><?php echo e($doctor->docname); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                           </td>
                           <td width="12%">
                                <input type="submit"  name="filter" value=" Filter" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                           </form>
                           </td>
                        </tr>
                        </table>
                        </center>
                    </td>
                </tr>
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                        <tr>
                            <th class="table-headin">Patient name</th>
                            <th class="table-headin">Appointment number</th>
                            <th class="table-headin">Doctor</th>
                            <th class="table-headin">Session Title</th>
                            <th class="table-headin" style="font-size:10px">Session Date & Time</th>
                            <th class="table-headin">Appointment Date</th>
                            <th class="table-headin">Events</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr >
                                    <td style="font-weight:600;"> &nbsp;
                                        <?php echo e(substr($appointment->pname, 0, 25)); ?>

                                    </td>
                                    <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                        <?php echo e($appointment->apponum); ?>

                                    </td>
                                    <td><?php echo e(substr($appointment->docname, 0, 25)); ?></td>
                                    <td><?php echo e(substr($appointment->title, 0, 15)); ?></td>
                                    <td style="text-align:center;font-size:12px;">
                                        <?php echo e(substr($appointment->scheduledate, 0, 10)); ?> <br><?php echo e(substr($appointment->scheduletime, 0, 5)); ?>

                                    </td>
                                    <td style="text-align:center;">
                                        <?php echo e($appointment->appodate); ?>

                                    </td>
                                    <td>
                                        <div style="display:flex;justify-content: center;">
                                            <a href="?action=drop&id=<?php echo e($appointment->appoid); ?>&name=<?php echo e($appointment->pname); ?>&apponum=<?php echo e($appointment->apponum); ?>" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel</font></button></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7">
                                        <br><br><br><br>
                                        <center>
                                        <img src="<?php echo e(asset('img/notfound.svg')); ?>" width="25%">
                                        <br>
                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn't find anything related to your keywords !</p>
                                        <a class="non-style-link" href="<?php echo e(url('/admin/appointments')); ?>"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</button></a>
                                        </center>
                                        <br><br><br><br>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                        </table>
                        </div>
                        </center>
                   </td> 
                </tr>
            </table>
        </div>
    </div>
    
    <?php if(request('action') == 'drop'): ?>
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <h2>Are you sure?</h2>
            <a class="close" href="<?php echo e(url('/admin/appointments')); ?>">&times;</a>
            <div class="content">
                You want to delete this record<br><br>
                Patient Name: &nbsp;<b><?php echo e(substr(request('name'), 0, 40)); ?></b><br>
                Appointment number &nbsp; : <b><?php echo e(substr(request('apponum'), 0, 40)); ?></b><br><br>
            </div>
            <div style="display: flex;justify-content: center;">
                <a href="<?php echo e(url('/admin/appointments/delete/' . request('id'))); ?>" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                <a href="<?php echo e(url('/admin/appointments')); ?>" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>
            </div>
        </center>
        </div>
    </div>
    <?php endif; ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\HealthcareApp_Laravel\resources\views/admin/appointments.blade.php ENDPATH**/ ?>