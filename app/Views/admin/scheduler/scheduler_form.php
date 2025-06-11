<form>
    <input type="hidden" id="updateID" name="updateID" value="">
    <!-- Day Selection -->
    <div class="form-floating mb-3">
        <input type="date" class="form-select" id="floatingDay" name="day" value="" required>
        <label for="floatingDay">Day</label>
    </div>

    <!-- Time From -->
    <div class="form-floating mb-3">
        <input type="time" class="form-control time" id="floatingTimeFrom" name="time_from" placeholder="Time From" required>
        <label for="floatingTimeFrom">Time From</label>
    </div>

    <!-- Time To -->
    <div class="form-floating mb-3">
        <input type="time" class="form-control time" id="floatingTimeTo" name="time_to" placeholder="Time To" required>
        <label for="floatingTimeTo">Time To</label>
    </div>

    <!-- Session Title -->
    <div class="form-floating mb-3">
        <input type="text" class="form-control" id="floatingSessionTitle" name="session_title" placeholder="Session Title" required>
        <label for="floatingSessionTitle">Session Title</label>
    </div>

    <!-- Session Description -->
    <div class="form-floating mb-3">
        <textarea class="form-control" id="floatingSessionDescription" name="session_description" placeholder="Session Description" style="height: 100px;"></textarea>
        <label for="floatingSessionDescription">Session Description</label>
    </div>

    <!-- Session Type -->
    <div class="form-floating mb-3">
        <select class="form-select" id="floatingSessionType" name="session_type">
            <option value="" selected>-- Select --</option>
            <option value="Type 1">Type 1</option>
            <option value="Type 2">Type 2</option>
        </select>
        <label for="floatingSessionType">Session Type</label>
    </div>

    <!-- Duration of Talk -->
    <div class="form-floating mb-3">
        <input type="number" class="form-control" id="floatingDurationTalk" name="duration_talk" placeholder="Duration of Talk" value="15" required>
        <label for="floatingDurationTalk">Duration of Talk (minutes)</label>
    </div>

    <!-- Duration of Break -->
    <div class="form-floating mb-3">
        <input type="number" class="form-control" id="floatingDurationBreak" name="duration_break" placeholder="Duration of Break" value="0" required>
        <label for="floatingDurationBreak">Duration of Break (minutes)</label>
    </div>

    <!-- Session Number -->
    <div class="form-floating mb-3">
        <input type="text" class="form-control" id="floatingSessionNumber" name="session_number" placeholder="Session Number">
        <label for="floatingSessionNumber">Session Number</label>
    </div>

    <!-- Event Rooms -->
    <div class="mb-3">
        <select class="form-select" id="floatingRooms" name="rooms">
        </select>
        <label for="floatingRooms">Rooms</label>
    </div>

    <!-- Event Rooms -->
    <div class="mb-3">
        <select class="form-select" id="floatingSessionTracks" name="session_track">
        </select>
        <label for="floatingSessionTracks">Tracks</label>
    </div>

    <!-- Session Chair 1 -->
    <div class="form-floating mb-3">
        <select class="form-select sessionChairSelect" id="floatingSessionChair1" name="session_chair[]">
            <option value="" selected>-- Select --</option>
        </select>
        <label for="floatingSessionChair1">Session Chair 1</label>
    </div>

    <!-- Session Chair 2 -->
    <div class="form-floating mb-3">
        <select class="form-select sessionChairSelect" id="floatingSessionChair2" name="session_chair[]">
            <option value="" selected>-- Select --</option>
        </select>
        <label for="floatingSessionChair2">Session Chair 2</label>
    </div>

    <!-- Session Chair 3 -->
    <div class="form-floating mb-3">
        <select class="form-select sessionChairSelect" id="floatingSessionChair3" name="session_chair[]">
            <option value="" selected>-- Select --</option>
        </select>
        <label for="floatingSessionChair3">Session Chair 3</label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
