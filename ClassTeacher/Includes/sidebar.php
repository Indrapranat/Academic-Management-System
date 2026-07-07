<?php
// ClassTeacher/Includes/sidebar.php
?>
<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">

  <a class="sidebar-brand d-flex align-items-center bg-gradient-primary justify-content-center" href="index.php">
    <div class="sidebar-brand-icon">
      <img src="img/logo/attnlg.png" style="width:45px;">
    </div>
    <div class="sidebar-brand-text mx-3">AMS</div>
  </a>

  <hr class="sidebar-divider my-0">

  <li class="nav-item active">
    <a class="nav-link" href="index.php">
      <i class="fas fa-fw fa-tachometer-alt"></i>
      <span>Dashboard</span>
    </a>
  </li>

  <hr class="sidebar-divider">

  <div class="sidebar-heading">Students</div>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStudents"
       aria-expanded="true" aria-controls="collapseStudents">
      <i class="fas fa-user-graduate"></i>
      <span>Manage Students</span>
    </a>
    <div id="collapseStudents" class="collapse" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Students</h6>
        <a class="collapse-item" href="viewStudents.php">View Students</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">

  <div class="sidebar-heading">Attendance</div>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAttendance"
       aria-expanded="true" aria-controls="collapseAttendance">
      <i class="fa fa-calendar-alt"></i>
      <span>Manage Attendance</span>
    </a>
    <div id="collapseAttendance" class="collapse" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Classic Attendance</h6>
        <a class="collapse-item" href="takeAttendance.php">Take Attendance</a>
        <a class="collapse-item" href="viewAttendance.php">View Class Attendance</a>
        <a class="collapse-item" href="viewStudentAttendance.php">View Student Attendance</a>
        <a class="collapse-item" href="downloadRecord.php">Today's Report (xls)</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">

  <div class="sidebar-heading">Meetings</div>
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMeetings"
       aria-expanded="true" aria-controls="collapseMeetings">
      <i class="fas fa-handshake"></i>
      <span>Manage Meetings</span>
    </a>

    <div id="collapseMeetings" class="collapse" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Meetings Control</h6>

        <!-- PENTING: nama file kamu 'newmeeting.php' (huruf kecil),
             di sidebar kamu tulis 'newMeeting.php' (M besar). Di Windows sering lolos, di Linux mati.
             Saya samakan ke yang kamu punya: newmeeting.php -->
        <a class="collapse-item" href="newmeeting.php">Create Meeting</a>

        <a class="collapse-item" href="meetingList.php">View Meetings</a>
        <a class="collapse-item" href="exportAllMeetings.php">Export All Meetings (xls)</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">
  <div class="version" id="version-ruangadmin"></div>

</ul>
