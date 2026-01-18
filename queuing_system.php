<?php session_start(); ?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Conchu | Barangay Information System</title>
  <link href="assets/css/theme.min.css" rel="stylesheet">
  <link href="assets/css/user.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    @keyframes blink { 0%,50%,100%{opacity:1;} 25%,75%{opacity:0;} }
    .blink { animation: blink 1s linear infinite; }
    .table-responsive { max-height: 50vh; overflow-y: auto; }
    td { text-align: center; font-size: 25px; white-space: nowrap; }
    th { text-align: center; }
    #active-queue { font-size: 80px; }
    .card-body h1 { margin-bottom: 0.5rem; }
  </style>
</head>
<body>
<main class="main" id="top">
  <section class="py-0 overflow-hidden position-relative vh-100" id="banner">
    <div class="bg-holder overlay" style="background-image:url('assets/img/generic/bg-1.jpg');background-position:center;background-size:cover;height:100vh;width:100%;"></div>
    <div class="container position-relative z-index-1 h-100 d-flex align-items-start">
      <div class="row g-0 w-100">

        <div class="col-lg-8 pe-lg-2">
          <!-- Active Queue Display -->
          <div class="card mb-3 text-center">
            <div class="card-body">
              <h1>QUEUE NUMBER</h1>
              <h1 id="active-queue">0000</h1>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <!-- Next Numbers Table -->
              <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Next Numbers</h5></div>
                <div class="card-body pt-0">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                      <thead class="bg-200 text-900">
                        <tr>
                          <th>Regular</th>
                          <th>Priority</th>
                        </tr>
                      </thead>
                    </table>
                    <div class="table-responsive" style="max-height:50vh; overflow-y:auto;">
                      <table class="table table-bordered table-striped mb-0">
                        <tbody id="queue-table"></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <!-- Past Numbers Table -->
              <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Past Numbers</h5></div>
                <div class="card-body pt-0">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                      <thead class="bg-200 text-900">
                        <tr>
                          <th>Regular</th>
                          <th>Priority</th>
                        </tr>
                      </thead>
                    </table>
                    <div class="table-responsive" style="max-height:50vh; overflow-y:auto;">
                      <table class="table table-bordered table-striped mb-0">
                        <tbody id="past-queue-table"></tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 ps-lg-2">
          <div class="sticky-sidebar text-center">
            <div class="card mb-3">
              <div class="card-body">
                <img src="assets/img/generic/conchu_logo.png" class="img-fluid" />
              </div>
            </div>
            <button class="btn btn-primary" id="getQueueNumberButton">Get a Queue Number</button>
          </div>
        </div>

      </div>
    </div>
  </section>
</main>

<script>
let previousActiveQueue = null;
let audioAllowed = false;

const fetchQueueData = () => {
  $.getJSON('fetch_queue.php', function(data){
    // --- Active Number Display ---
    const activeQueue = data.active?.queue_number || '';
    const activePriority = data.active?.priority_label==='Priority' ? 'Priority' : '';
    const activeEl = document.getElementById('active-queue');
    activeEl.innerHTML = activeQueue + (activePriority ? ' <span class="badge bg-danger">'+activePriority+'</span>' : '');
    activeEl.classList.remove('blink'); void activeEl.offsetWidth; activeEl.classList.add('blink');

    if(previousActiveQueue && previousActiveQueue !== activeQueue && audioAllowed){
      new Audio('sound_que.mp3').play().catch(()=>{});
    }
    previousActiveQueue = activeQueue;

    // --- Next Numbers Table (pending only, exclude active) ---
    const tbodyNext = document.getElementById('queue-table');
    tbodyNext.innerHTML = '';
    data.pending.forEach(p => {
      const row = document.createElement('tr');
      row.innerHTML = p.priority_label==='Regular' 
        ? `<td>${p.queue_number}</td><td></td>` 
        : `<td></td><td>${p.queue_number}</td>`;
      tbodyNext.appendChild(row);
    });

    // --- Past Numbers Table ---
    const tbodyPast = document.getElementById('past-queue-table');
    tbodyPast.innerHTML = '';
    (data.past.regular_done||[]).forEach(num => {
      const row = document.createElement('tr');
      row.innerHTML = `<td>${num}</td><td></td>`;
      tbodyPast.appendChild(row);
    });
    (data.past.priority_done||[]).forEach(num => {
      const row = document.createElement('tr');
      row.innerHTML = `<td></td><td>${num}</td>`;
      tbodyPast.appendChild(row);
    });
  });
};

// --- Button to get new queue number ---
$('#getQueueNumberButton').click(async function(){
  if(!audioAllowed) audioAllowed = true;
  const {isConfirmed} = await Swal.fire({
    title:'Priority Check',
    text:'Are you a PWD or Senior Citizen (Priority)?',
    icon:'question',
    showCancelButton:true,
    confirmButtonText:'Yes',
    cancelButtonText:'No'
  });
  const priority = isConfirmed ? 1 : 0;

  $.post('add_queue.php', {name:'', priority:priority}, function(data){
    if(data.success){
      Swal.fire({
        title:'New Queue Number', 
        html:`Queue: <strong>${data.queue_number}</strong><br>Priority: <strong>${priority===1?'Yes':'No'}</strong>`, 
        icon:'success'
      });
      fetchQueueData();
    } else {
      Swal.fire({title:'Error', text:'Failed to get queue number', icon:'error'});
    }
  }, 'json');
});

fetchQueueData();
setInterval(fetchQueueData, 3000);
</script>
</body>
</html>
