
<?php

require "calendar.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=David+Libre:wght@400;500;700&family=Edu+NSW+ACT+Cursive:wght@400..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playwrite+MX+Guides&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Document</title>
</head>
<body>
    <header>
        <h1>
            Calendar Project
        </h1>
    </header>   

        <!-- ✅ Success / Error Messages -->
    <?php if ($successMsg): ?>
        <div class="alert success"><?= $successMsg ?></div>
    <?php elseif ($errorMsg): ?>
        <div class="alert error"><?= $errorMsg ?></div>
    <?php endif; ?>

    <!-- clock -->
     
    <div class="clock-container">
        <div id="clock"></div>
    </div>

    <!-- calendar section -->
     <div class="calendar">
        <div class="nav-btn-container">
            <button class="nav-btn" onclick="changeMonth(-1)">
                ⏮️
            </button>
            <h2 id="monthYear" style="margin: 0px;"></h2>
            <button class="nav-btn" onclick="changeMonth(1)">
               ⏭️
            </button>
        </div>
        <div class="calendar-grid" id="calendar"></div>
     </div>

     <!-- Modal for add/edit/delete -->
    <div class="modal" id="eventModal">  
        <div class="modal-content">
            <div id="eventSelectorWrapper">
                <label for="eventSelector">
                    <strong>
                        Select Event:
                    </strong>
                </label>
                <select id="eventSelector" onchange="handleEventSelection(this.value)">
                    <option disabled selected>Choose Event</option>
                </select>
            </div>
  

     <!-- Main Form -->
      <form method="POST" id="eventForm">
            <input type="hidden" name="action" id="formAction" value="add">
            <input type="hidden" name="event_id" id="eventId">
            
            <label for="courseName">Course Title:</label>
            <input type="text" name="course_name" id="courseName" require>

            <label for="instructorName">Instructor Name:</label>
            <input type="text" name="instructor_name" id="instructorName" require>

            <label for="startDate">Start Date:</label>
            <input type="date" name="start_date" id="startDate" require>   

            <label for="endDate">End Date:</label>
            <input type="date" name="end_date" id="endDate" require>

            <label for="startTime">Start Time:</label>
            <input type="time" name="start_time" id="startTime" required>

            <label for="endTime">End Time:</label>
            <input type="time" name="end_time" id="endTime" required>

            <button type="submit">Save</button>
      </form>

      <!-- Delete Form -->
       <form method="POST" onsubmit="return confirm('Are you sure you want to delete this appoinment ?')">
        <input type="hidden" name="action" value="delete"> 
        <input type="hidden" name="event_id" id="deleteEventId">
        <button type="submit" class="submit-btn">Delete</button> 
       </form>

       <!-- Cancel Button -->
       <button type="button" class="submit-btn" onclick="closeModal()">Cancel</button>
       
        </div> 
     </div>  

  <script>
    const events = <?= json_encode($eventsFromDb, JSON_UNESCAPED_UNICODE); ?>;
  </script>

        <script src="calendar.js"></script>
</body>
</html>