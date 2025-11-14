<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo e(asset('css/animations.css')); ?>">  
    <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">  
    <link rel="stylesheet" href="<?php echo e(asset('css/admin.css')); ?>">
    <title>Doctors</title>
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
                    <td class="menu-btn menu-icon-doctor menu-active menu-icon-doctor-active">
                        <a href="<?php echo e(url('/admin/doctors')); ?>" class="non-style-link-menu non-style-link-menu-active"><div><p class="menu-text">Doctors</p></a></div>
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
                    <td class="menu-btn menu-icon-patient">
                        <a href="<?php echo e(url('/admin/patients')); ?>" class="non-style-link-menu"><div><p class="menu-text">Patients</p></a></div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%">
                        <a href="<?php echo e(url('/admin/doctors')); ?>" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Back</font></button></a>
                    </td>
                    <td>
                        <form action="<?php echo e(url('/admin/doctors')); ?>" method="get" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email" list="doctors" value="<?php echo e($searchKeyword ?? ''); ?>">&nbsp;&nbsp;
                            
                            <datalist id="doctors">
                                <?php $__currentLoopData = $doctor_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($doctor->docname); ?>">
                                    <option value="<?php echo e($doctor->docemail); ?>">
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
               
                <tr >
                    <td colspan="2" style="padding-top:30px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Add New Doctor</p>
                    </td>
                    <td colspan="2">
                        <a href="?action=add&id=none&error=0" class="non-style-link"><button  class="login-btn btn-primary btn button-icon"  style="display: flex;justify-content: center;align-items: center;margin-left:75px;background-image: url('../img/icons/add.svg');">Add New</font></button>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">All Doctors (<?php echo e($doctor_count); ?>)</p>
                    </td>
                </tr>
                
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                        <tr>
                            <th class="table-headin">Doctor Name</th>
                            <th class="table-headin">Email</th>
                            <th class="table-headin">Specialties</th>
                            <th class="table-headin">Events</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td> &nbsp;<?php echo e(substr($doctor->docname, 0, 30)); ?></td>
                                    <td><?php echo e(substr($doctor->docemail, 0, 20)); ?></td>
                                    <td><?php echo e(substr($doctor->spcil_name, 0, 20)); ?></td>
                                    <td>
                                        <div style="display:flex;justify-content: center;">
                                            <a href="?action=edit&id=<?php echo e($doctor->docid); ?>&error=0" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-edit"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Edit</font></button></a>
                                            &nbsp;&nbsp;&nbsp;
                                            <a href="?action=view&id=<?php echo e($doctor->docid); ?>" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                            &nbsp;&nbsp;&nbsp;
                                            <a href="?action=drop&id=<?php echo e($doctor->docid); ?>&name=<?php echo e($doctor->docname); ?>" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Remove</font></button></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4">
                                        <br><br><br><br>
                                        <center>
                                        <img src="<?php echo e(asset('img/notfound.svg')); ?>" width="25%">
                                        <br>
                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn't find anything related to your keywords !</p>
                                        <a class="non-style-link" href="<?php echo e(url('/admin/doctors')); ?>"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Doctors &nbsp;</button></a>
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

    <?php if($modalType == 'drop' && $modalData): ?>
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <h2>Are you sure?</h2>
            <a class="close" href="<?php echo e(url('/admin/doctors')); ?>">&times;</a>
            <div class="content">
                You want to delete this record<br>(<?php echo e(substr($modalData['name'], 0, 40)); ?>).
            </div>
            <div style="display: flex;justify-content: center;">
                <a href="<?php echo e(url('/admin/doctors/delete/' . $modalData['id'])); ?>" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;Yes&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                <a href="<?php echo e(url('/admin/doctors')); ?>" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;No&nbsp;&nbsp;</font></button></a>
            </div>
        </center>
        </div>
    </div>
    <?php endif; ?>

    <?php if($modalType == 'view' && $modalData): ?>
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <h2></h2>
            <a class="close" href="<?php echo e(url('/admin/doctors')); ?>">&times;</a>
            <div class="content">eDoc Web App<br></div>
            <div style="display: flex;justify-content: center;">
            <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                <tr><td><p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">View Details.</p><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Name: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->docname); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Email: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->docemail); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">NIC: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->docnic); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Telephone: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->doctel); ?><br><br></td></tr>
                <tr><td class="label-td" colspan="2"><label class="form-label">Specialties: </label></td></tr>
                <tr><td class="label-td" colspan="2"><?php echo e($modalData->spcil_name); ?><br><br></td></tr>
                <tr><td colspan="2"><a href="<?php echo e(url('/admin/doctors')); ?>"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a></td></tr>
            </table>
            </div>
        </center>
        <br><br>
        </div>
    </div>
    <?php endif; ?>

    <?php if($modalType == 'add' || ($modalType == 'add' && $error_code != '4' && $error_code != '0')): ?>
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <a class="close" href="<?php echo e(url('/admin/doctors')); ?>">&times;</a> 
            <div style="display: flex;justify-content: center;">
            <div class="abc">
            <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                <?php if($error_message): ?>
                <tr><td class="label-td" colspan="2" style="color:rgb(255, 62, 62);text-align:center;"><?php echo e($error_message); ?></td></tr>
                <?php endif; ?>
                <tr><td><p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Add New Doctor.</p><br><br></td></tr>
                
                <form action="<?php echo e(url('/admin/doctors/add')); ?>" method="POST" class="add-new-form">
                    <?php echo csrf_field(); ?>
                    <tr><td class="label-td" colspan="2"><label for="name" class="form-label">Name: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="text" name="name" class="input-text" placeholder="Doctor Name" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="Email" class="form-label">Email: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="email" name="email" class="input-text" placeholder="Email Address" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="nic" class="form-label">NIC: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="text" name="nic" class="input-text" placeholder="NIC Number" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="Tele" class="form-label">Telephone: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="tel" name="Tele" class="input-text" placeholder="Telephone Number" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="spec" class="form-label">Choose specialties: </label></td></tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <select name="spec" id="" class="box" >
                                <?php $__currentLoopData = $specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($specialty->id); ?>"><?php echo e($specialty->sname); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select><br>
                        </td>
                    </tr>
                    <tr><td class="label-td" colspan="2"><label for="password" class="form-label">Password: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="password" name="password" class="input-text" placeholder="Defind a Password" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="cpassword" class="form-label">Conform Password: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="password" name="cpassword" class="input-text" placeholder="Conform Password" required><br></td></tr>
                    <tr>
                        <td colspan="2">
                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="submit" value="Add" class="login-btn btn-primary btn">
                        </td>
                    </tr>
                </form>
            </table>
            </div>
            </div>
        </center>
        <br><br>
        </div>
    </div>
    <?php endif; ?>

    <?php if($modalType == 'add' && $error_code == '4'): ?>
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <br><br><br><br>
            <h2>New Record Added Successfully!</h2>
            <a class="close" href="<?php echo e(url('/admin/doctors')); ?>">&times;</a>
            <div class="content"></div>
            <div style="display: flex;justify-content: center;">
                <a href="<?php echo e(url('/admin/doctors')); ?>" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
            </div>
            <br><br>
        </center>
        </div>
    </div>
    <?php endif; ?>

    <?php if($modalType == 'edit' || ($modalType == 'edit' && $error_code != '4' && $error_code != '0')): ?>
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <a class="close" href="<?php echo e(url('/admin/doctors')); ?>">&times;</a> 
            <div style="display: flex;justify-content: center;">
            <div class="abc">
            <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                <?php if($error_message): ?>
                <tr><td class="label-td" colspan="2" style="color:rgb(255, 62, 62);text-align:center;"><?php echo e($error_message); ?></td></tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Edit Doctor Details.</p>
                        Doctor ID : <?php echo e($modalData->docid ?? ''); ?> (Auto Generated)<br><br>
                    </td>
                </tr>
                <form action="<?php echo e(url('/admin/doctors/update')); ?>" method="POST" class="add-new-form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" value="<?php echo e($modalData->docid ?? ''); ?>" name="id00">
                    <input type="hidden" name="oldemail" value="<?php echo e($modalData->docemail ?? ''); ?>" >
                    <tr><td class="label-td" colspan="2"><label for="Email" class="form-label">Email: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="email" name="email" class="input-text" placeholder="Email Address" value="<?php echo e($modalData->docemail ?? ''); ?>" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="name" class="form-label">Name: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="text" name="name" class="input-text" placeholder="Doctor Name" value="<?php echo e($modalData->docname ?? ''); ?>" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="nic" class="form-label">NIC: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="text" name="nic" class="input-text" placeholder="NIC Number" value="<?php echo e($modalData->docnic ?? ''); ?>" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="Tele" class="form-label">Telephone: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="tel" name="Tele" class="input-text" placeholder="Telephone Number" value="<?php echo e($modalData->doctel ?? ''); ?>" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="spec" class="form-label">Choose specialties: (Current <?php echo e($modalData->spcil_name ?? ''); ?>)</label></td></tr>
                    <tr>
                        <td class="label-td" colspan="2">
                            <select name="spec" id="" class="box">
                                <?php $__currentLoopData = $specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($specialty->id); ?>" <?php echo e(($modalData->specialties == $specialty->id) ? 'selected' : ''); ?>>
                                        <?php echo e($specialty->sname); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select><br><br>
                        </td>
                    </tr>
                    <tr><td class="label-td" colspan="2"><label for="password" class="form-label">Password: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="password" name="password" class="input-text" placeholder="Defind a New Password" required><br></td></tr>
                    <tr><td class="label-td" colspan="2"><label for="cpassword" class="form-label">Conform Password: </label></td></tr>
                    <tr><td class="label-td" colspan="2"><input type="password" name="cpassword" class="input-text" placeholder="Conform Password" required><br></td></tr>
                    <tr>
                        <td colspan="2">
                            <input type="reset" value="Reset" class="login-btn btn-primary-soft btn" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="submit" value="Save" class="login-btn btn-primary btn">
                        </td>
                    </tr>
                </form>
            </table>
            </div>
            </div>
        </center>
        <br><br>
        </div>
    </div>
    <?php endif; ?>

    <?php if($modalType == 'edit' && $error_code == '4'): ?>
    <div id="popup1" class="overlay">
        <div class="popup">
        <center>
            <br><br><br><br>
            <h2>Edit Successfully!</h2>
            <a class="close" href="<?php echo e(url('/admin/doctors')); ?>">&times;</a>
            <div style="display: flex;justify-content: center;">
                <a href="<?php echo e(url('/admin/doctors')); ?>" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
            </div>
            <br><br>
        </center>
        </div>
    </div>
    <?php endif; ?>
</body>
</html><?php /**PATH C:\xampp\htdocs\HealthcareApp_Laravel\resources\views/admin/doctors.blade.php ENDPATH**/ ?>