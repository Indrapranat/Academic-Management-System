<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
  <a class="sidebar-brand d-flex align-items-center bg-gradient-primary justify-content-center" href="index.php">
    <div class="sidebar-brand-icon">
      <img src="img/logo/attnlg.png">
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

  <!-- KELAS & ROMBEL -->
  <div class="sidebar-heading">
    Kelas & Rombel
  </div>

  <!-- Kelola Kelas -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseKelas"
      aria-expanded="true" aria-controls="collapseKelas">
      <i class="fas fa-chalkboard"></i>
      <span>Kelola Kelas</span>
    </a>
    <div id="collapseKelas" class="collapse" aria-labelledby="headingKelas" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Kelas</h6>
        <a class="collapse-item" href="createClass.php">Tambah Kelas</a>
        <a class="collapse-item" href="viewClasses.php">Daftar Kelas</a>
      </div>
    </div>
  </li>

  <!-- Kelola Rombel -->
  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRombel"
      aria-expanded="true" aria-controls="collapseRombel">
      <i class="fas fa-code-branch"></i>
      <span>Kelola Rombel</span>
    </a>
    <div id="collapseRombel" class="collapse" aria-labelledby="headingRombel" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Rombel</h6>
        <a class="collapse-item" href="createClassArms.php">Tambah Rombel</a>
        <a class="collapse-item" href="viewClassArms.php">Daftar Rombel</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">

  <!-- WALI KELAS -->
  <div class="sidebar-heading">
    Wali Kelas
  </div>

  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTeachers"
      aria-expanded="true" aria-controls="collapseTeachers">
      <i class="fas fa-chalkboard-teacher"></i>
      <span>Kelola Wali Kelas</span>
    </a>
    <div id="collapseTeachers" class="collapse" aria-labelledby="headingTeachers" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Wali Kelas</h6>
        <a class="collapse-item" href="createClassTeacher.php">Tambah Wali Kelas</a>
        <a class="collapse-item" href="viewClassTeachers.php">Daftar Wali Kelas</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">

  <!-- SISWA -->
  <div class="sidebar-heading">
    Siswa
  </div>

  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStudents"
      aria-expanded="true" aria-controls="collapseStudents">
      <i class="fas fa-user-graduate"></i>
      <span>Kelola Siswa</span>
    </a>
    <div id="collapseStudents" class="collapse" aria-labelledby="headingStudents" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Siswa</h6>
        <a class="collapse-item" href="createStudents.php">Tambah Siswa</a>
        <a class="collapse-item" href="viewStudents.php">Daftar Semua Siswa</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">

  <!-- TAHUN AJARAN & SEMESTER -->
  <div class="sidebar-heading">
    Tahun Ajaran & Semester
  </div>

  <li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSessionTerm"
      aria-expanded="true" aria-controls="collapseSessionTerm">
      <i class="fa fa-calendar-alt"></i>
      <span>Kelola Tahun Ajaran & Semester</span>
    </a>
    <div id="collapseSessionTerm" class="collapse" aria-labelledby="headingSessionTerm" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">Tahun Ajaran & Semester</h6>
        <a class="collapse-item" href="createSessionTerm.php">Kelola Session & Term</a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">
  <div class="version" id="version-ruangadmin"></div>
</ul>
