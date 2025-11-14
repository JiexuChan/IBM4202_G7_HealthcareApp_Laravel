<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo e(asset('css/animations.css')); ?>">  
    <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">  
    <link rel="stylesheet" href="<?php echo e(asset('css/admin.css')); ?>">
    <title>Patients</title>
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
                    <td class="menu-btn menu-icon-schedule">
                        <a href="<?php echo e(url('/admin/schedule')); ?>" class="non-style-link-menu"><div><p class="menu-text">Schedule</p></div></a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="<?php echo e(url('/admin/appointments')); ?>" class="non-style-link-menu"><div><p class="menu-text">Appointment</p></a></div>
                    </td>
                </tr>
                <tr class="menu-row" >
                    <td class="menu-btn menu-icon-patient  menu-active menu-icon-patient-active">
                        <a href="<?php echo e(url('/admin/patients')); ?>" class="non-style-link-menu  non-style-link-menu-active"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%">
                        <a href="<?php echo e(url('/admin/patients')); ?>" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <form action="<?php echo e(url('/admin/patients')); ?>" method="get" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Patient name or Email" list="patient" value="<?php echo e(request('search') ?? ''); ?>">&nbsp;&nbsp;
                            
                            <datalist id="patient">
                                <?php $__currentLoopData = $patient_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($patient->pname); ?>">
                                    <option value="<?php echo e($patient->pemail); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </datalist>
                            
                            <input type="Submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px;padding-right: 25px;padding-top: 10px;padding-bottom: 10px;">
                        </form>
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
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Patients (<?php echo e($patient_count); ?>)</p>
                    </td>
                </tr>
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown"  style="border-spacing:0;">
                        <thead>
                        <tr>
                            <th class="table-headin">Name</th>
                            <th class="table-headin">NIC</th>
                            <th class="table-headin">Telephone</th>
                            <th class="table-headin">Email</th>
                            <th class="table-headin">Date of Birth</th>
                            <th class="table-headin">Events</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td> &nbsp;<?php echo e(substr($patient->pname, 0, 35)); ?></td>
                                    <td><?php echo e(substr($patient->pnic, 0, 12)); ?></td>
                                    <td><?php echo e(substr($patient->ptel, 0, 10)); ?></td>
                                    <td><?php echo e(substr($patient->pemail, 0, 20)); ?></td>
                                    <td><?php echo e(substr($patient->pdob, 0, 10)); ?></td>
                                    <td >
                                        <div style="display:flex;justify-content: center;">
                                            <a href="?action=view&id=<?php echo e($patient->pid); ?>" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6">
                                        <br><br><br><br>
                                        <center>
                                        <img src="<?php echo e(asset('img/notfound.svg')); ?>" width="25%">
                                        <br>
                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn't find anything related to your keywords !</p>
                                        <a class="non-style-link" href="<?php echo e(url('/admin/patients')); ?>"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Patients &nbsp;</button></a>
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

    <?php if($modalType == 'view' && $modalData): ?>
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <a class="close" href="<?php echo e(url('/admin/patients')); ?>">&times;</a>
            <div class="content"></div>
            <div style="display: flex;justify-content: center;">
            <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                <tr><td><p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Patient ID: </label></td></tr>
                <tr><td class="label-td" colspan="2">P-<?php echo e($modalData->pid); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Name: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->pname); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Email: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->pemail); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">NIC: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->pnic); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Telephone: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->ptel); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Address: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->paddress); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Date of Birth: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->pdob); ?><br><br></td></tr>
                <tr><td colspan="2"><a href="<?php echo e(url('/admin/patients')); ?>"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a></td></tr>
            </table>
            </div>
        </center>
        <br><br>
        </div>
    </div>
    <?php endif; ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\HealthcareApp_Laravel\resources\views/admin/patients.blade.php ENDPATH**/ ?>