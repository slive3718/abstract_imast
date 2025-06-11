
<?php echo view('admin/common/menu'); ?>

<style>
    .fc-disabled-day {
        background-color: BLACK !important; /* Gray background for disabled dates */
        pointer-events: none; /* Disable click events */
        opacity: 0.6; /* Make the dates appear faded */
    }

    .fc-disabled-slot td{
        background-color: BLACK !important; /* Gray background for disabled dates */
        pointer-events: none; /* Disable click events */
        opacity: 0.6; /* Make the dates appear faded */
    }

    /* Style for the entire event container */
    .event-container {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        background-color: rgba(119, 168, 208, 0.65);
        border-radius: 5px;
        border: 1px solid #007bff;
        color: white ;
        overflow-x: auto
    }

    /* Style for the buttons container */
    .event-buttons-container {
        display: flex;
        gap: 5px; /* Space between buttons */
        margin-top: 5px;
    }

    /* Style for the buttons */
    .event-edit-button, .event-talks-button {
        border: none;
        border-radius: 3px;
        padding: 3px 8px;
        cursor: pointer;
        font-size: 0.85em;
    }

    /* Style for the buttons */
    .event-delete-button, .delete-talks-button {
        border: none;
        border-radius: 3px;
        padding: 3px 8px;
        cursor: pointer;
        font-size: 0.85em;
        background-color: red;
        color: white;
    }

    .event-edit-button:hover, .event-talks-button:hover {
        opacity: 2;
        color: white !important
    }

    .event-edit-button{
        background-color: #0087ff;
        color: white !important
    }

    .event-talks-button{
        background-color: #32b934;
        color: white !important
    }
    /* Ensure events do not overflow */
    .fc-event {
        white-space: normal !important; /* Allow multiline text */
        word-wrap: break-word;

    }

    .fc-timegrid-event {
        overflow: visible; /* Allow content to overflow */
    }

    .event-buttons-container {
        color: white !important;
    }

    .event-container{
        font-size:12px
    }

    .fc-header-toolbar {
        position: sticky;
        top: 60px; /* Adjust this value based on the height of your top nav */
        z-index: 10; /* Ensure it stays above other elements */
        background-color: white; /* Optional: Add a background color to avoid overlap issues */
    }

    .fc-scrollgrid-section-sticky{
        position: sticky;
        top: 90px; /* Adjust this value based on the height of your top nav */
        z-index: 10; /* Ensure it stays above other elements */
        background-color: white; /* Optional: Add a background color to avoid overlap issues */
    }

    /* Style for when side nav is open */
    #sidenav.open {
        width: 150px;
    }
    
    /*!* Adjust main content when the nav is open *!*/
    #calendar.open {
        margin-left: 150px;
    }
    
    #calendar {
        transition: margin-left .5s;
        padding: 16px;
    }

    #abstract_list.open{
        display: block;
    }

    #abstract_list{
        display: none;
    }

</style>

<main>
    <div class="container-fluid p-0">
        <div class="card p-0 m-0 shadow-lg">
            <div style='float:left'>
                Timezone:
                <select id='time-zone-selector' disabled>
                    <?=view("admin/common/timezone")?>
                </select>
            </div>
            <div class="card-body" style="padding-bottom:150px">
                <div class="row">
                    <div id="sidenav" class="sidenav" style="width:80px; margin-top:20px">
                        <div class="">
                            <button class="btn btn-sm btn-primary" onclick="toggleNav()">Abstracts</button>
                            <div id="abstract_list" >

                            </div>
                        </div>
                    </div>
                    <div  style="width: calc(100% - 80px)">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<!-- Modal -->
<div class="modal fade shadow-lg" id="schedulerModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<!-- include moment and one of the moment-timezone builds -->
<script src='https://cdn.jsdelivr.net/npm/moment@2.29.4/min/moment.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/moment-timezone@0.5.40/builds/moment-timezone-with-data.min.js'></script>


<script src=" https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js "></script>

<!-- the connector. must go AFTER moment-timezone -->
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/moment-timezone@6.1.15/index.global.min.js'></script>

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    let baseUrlAdmin = "<?=base_url('admin/')?>"
    document.addEventListener('DOMContentLoaded', function() {

        renderCalendar();
        var eventCalendar;
    });

    function renderCalendar(){
        getDateAllowed().then(function(allowedDates){
            // console.log(allowedDates[0].date)
            var timeZoneSelectorEl = document.getElementById('time-zone-selector');
            var calendarEl = document.getElementById('calendar');
            let calendar =  eventCalendar = new FullCalendar.Calendar(calendarEl, {
                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                timeZone: 'local', // arbitrary timezones are now honored!
                initialView: 'roomView',
                navLinks: true, // can click day/week names to navigate views
                editable: true,
                selectable: true,
                // dayMaxEvents: true, // allow "more" link when too many events
                height: 'auto',
                initialDate: allowedDates[0].date ?? '2025-02-05', // Navigate to April 2025
                headerToolbar: {
                    left: 'customDays roomView prev,next',  // Define custom buttons
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                expandRows: true,
                contentHeight: 'auto',

                resources: async function(){
                    let rooms = await schedulerRooms();
                    return rooms.map(room => ({
                        id: room.room_id, // Use room_id as the resource ID
                        title: room.name // Use name as the resource title
                    }));
                },
                resourceOrder: 'room_id',
                resourceAreaColumns: [
                    {
                        headerContent: 'Rooms'
                    },
                ],
                resourceAreaWidth: 100,
                eventTimeFormat: { hour: 'numeric', minute: '2-digit', timeZoneName: 'short' },
                selectAllow: function(selectInfo) {
                    const selectedDateTime = selectInfo.start.toISOString();
                    return allowedDates.some(range => {
                        const rangeStart = new Date(range.startDateTime).toISOString();
                        const rangeEnd = new Date(range.endDateTime).toISOString();
                        return selectedDateTime >= rangeStart && selectedDateTime <= rangeEnd;
                    });
                },
                dayCellDidMount: function(info) {
                    const dateStr = info.date.toISOString().split('T')[0]; // Format date to YYYY-MM-DD
                    const isAllowed = allowedDates.some(range => {
                        const rangeStartDate = new Date(range.startDateTime).toISOString().split('T')[0];
                        const rangeEndDate = new Date(range.endDateTime).toISOString().split('T')[0];
                        return dateStr >= rangeStartDate && dateStr <= rangeEndDate;
                    });
                    if (!isAllowed) {
                        info.el.classList.add('fc-disabled-day'); // Add a custom class to disable non-allowed dates
                    }
                },
                eventContent: function(arg) {
                    let eventTitle = document.createElement('div');
                    eventTitle.innerHTML = arg.event.title;

// Edit button
                    let editButton = document.createElement('button');
                    editButton.innerHTML = 'Edit';
                    editButton.className = 'event-edit-button';
                    editButton.onclick = function (e) {
                        e.stopPropagation();
                        editEvent(arg.event, allowedDates);
                    };

// Talks button
                    let talksButton = document.createElement('button');
                    talksButton.innerHTML = 'Talks';
                    talksButton.className = 'event-talks-button';
                    talksButton.onclick = function (e) {
                        e.stopPropagation();
                        talksEvent(arg.event);
                    };

//
// // Edit button
//                     let deleteButton = document.createElement('button');
//                     deleteButton.innerHTML = 'Delete';
//                     deleteButton.className = 'event-delete-button';
//                     deleteButton.onclick = function (e) {
//                         e.stopPropagation();
//                         deleteEvent(arg.event, allowedDates);
//                     };

// Buttons container
                    let buttonsContainer = document.createElement('div');
                    buttonsContainer.className = 'event-buttons-container';
                    buttonsContainer.appendChild(editButton);
                    buttonsContainer.appendChild(talksButton);
                    // buttonsContainer.appendChild(deleteButton);

// Wrapper for event content
                    let eventContainer = document.createElement('div');
                    eventContainer.className = 'event-container';
                    eventContainer.appendChild(eventTitle);
                    eventContainer.appendChild(buttonsContainer);

                    return { domNodes: [eventContainer] };

                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    getScheduledEvents()
                        .then(function(events) {
                            // console.log(events)
                            successCallback(events);
                        })
                        .catch(function(error) {
                            failureCallback(error);
                        });
                },
                // Function to set custom time ranges per day based on your allowedDates array
                datesSet: function(info) {
                    const currentDate = info.view.currentStart;

                    // Format currentDate to YYYY-MM-DD
                    const dateKey = currentDate.toISOString().split('T')[0];

                    // Find the entry for the current date in allowedDates
                    const customTimeSlot = allowedDates.find(entry => entry.date === dateKey);

                    if (customTimeSlot) {
                        // Set the time range based on the current day's entry
                        const startTime = customTimeSlot.startDateTime.split(' ')[1]; // Extract time from "YYYY-MM-DD HH:mm:ss"
                        const endTime = customTimeSlot.endDateTime.split(' ')[1]; // Extract time from "YYYY-MM-DD HH:mm:ss"
                        calendar.setOption('slotMinTime', startTime);
                        calendar.setOption('slotMaxTime', endTime);
                    }else{
                        calendar.setOption('slotMinTime', '00:00:00');
                        calendar.setOption('slotMaxTime', '24:00:00');
                    }
                },
                views: {
                    timeGridDay: {
                        slotDuration: '00:05:00',
                        slotLabelInterval: 1,
                    },
                    listWeek: {
                        eventDidMount: function(info) {
                            // console.log(info);
                            // Add custom content to the list event's time element
                            const timeElement = info.el.querySelector(".fc-list-event-time");
                            if (timeElement) {
                                const customDiv = document.createElement("div");
                                customDiv.textContent = info.event.extendedProps.room_name;
                                timeElement.appendChild(customDiv);
                            }
                            
                            console.log(info.el.querySelectorAll('.acceptanceStatus'))
                            if (info.view.type === 'listWeek') {
                                info.el.querySelectorAll('.acceptanceStatus').forEach(el => {
                                    el.style.display = 'block'; // Show in listWeek
                                });
                            } else {
                                info.el.querySelectorAll('.acceptanceStatus').forEach(el => {
                                    el.style.display = 'none'; // Hide in other views
                                });
                            }
                        },
                        events: function(events){
                            return sortedEvents = events.sort((a, b) => {
                                if (a.extendedProps.room_name < b.extendedProps.room_name) return -1;
                                if (a.extendedProps.room_name > b.extendedProps.room_name) return 1;
                                return 0;
                            });
                        }, // Use the sorted events
                        eventContent: function(arg){
                            let eventTitle = document.createElement('div');
                            eventTitle.innerHTML = arg.event.title;

// Edit button
                            let editButton = document.createElement('button');
                            editButton.innerHTML = 'Edit';
                            editButton.className = 'event-edit-button';
                            editButton.onclick = function (e) {
                                e.stopPropagation();
                                editEvent(arg.event, allowedDates);
                            };

// Talks button
                            let talksButton = document.createElement('button');
                            talksButton.innerHTML = 'Talks';
                            talksButton.className = 'event-talks-button';
                            talksButton.onclick = function (e) {
                                e.stopPropagation();
                                talksEvent(arg.event);
                            };

                            let deleteButton = document.createElement('button');
                            deleteButton.innerHTML = 'Delete';
                            deleteButton.className = 'event-delete-button';
                            deleteButton.style.alignItems = 'right';
                            deleteButton.onclick = function (e) {
                                e.stopPropagation();
                                deleteEvent(arg.event, allowedDates);
                            };

// Buttons container
                            let buttonsContainer = document.createElement('div');
                            buttonsContainer.className = 'event-buttons-container';
                            buttonsContainer.appendChild(editButton);
                            buttonsContainer.appendChild(talksButton);
                            buttonsContainer.appendChild(deleteButton);

// Wrapper for event content
                            let eventContainer = document.createElement('div');
                            eventContainer.className = 'event-container';
                            eventContainer.appendChild(eventTitle);
                            eventContainer.appendChild(buttonsContainer);

                            return { domNodes: [eventContainer] };
                        }
                    },
                    timeGridWeek: {
                        slotDuration: '00:15:00',
                        slotLabelInterval: 1,
                    },
                    dayGridMonth: {
                        slotDuration: '00:15:00',
                        slotLabelInterval: 1,
                    },
                    customDays:{
                        slotDuration: '00:05:00',
                        slotLabelInterval: 1,
                    },
                    roomView: {
                        height: 100,
                        type: 'resourceTimeline', // Use resourceTimeline for rooms
                        duration: { days: 1 }, // Show 4 days
                        slotDuration: '00:15:00',
                        slotLabelInterval: "00:15",
                        expandRows: true,
                        overlap: false,

                    },
                    customWeek: {
                        type: 'resourceTimelineDay', // Use resourceTimeline for rooms
                        duration: { days: 4 }, // Show 4 days (Sat, Sun, Mon, Tue)
                        visibleRange: function (currentDate) {
                            // Calculate the range based on Saturday as the start of the week
                            let start = new Date(currentDate);
                            start.setDate(start.getDate() - start.getDay() + 6); // Previous Saturday
                            let end = new Date(start);
                            end.setDate(start.getDate() + 3); // Saturday + 3 days
                            return { start, end };
                        },
                        dayHeaderFormat: { weekday: 'short', day: '2-digit' }, // Display short weekday names
                        slotDuration: '00:05:00',
                        slotLabelInterval: 1,
                        // Function to set custom time ranges per day based on your allowedDates array
                        datesSet: function(info) {
                            calendar.setOption('slotMinTime', '00:00:00');
                            calendar.setOption('slotMaxTime', '24:00:00');
                        },
                    }
                },
                customButtons: {
                    customDays: {
                        text: 'Meeting Dates',
                        click: function () {
                            calendar.changeView('customWeek'); // Switch to the custom view
                            calendar.gotoDate(allowedDates[0].date); // Navigate to April 2025
                        }
                    },
                    roomView: {
                        text: 'Day View',
                        click: function () {
                            calendar.changeView('roomView'); // Switch to the custom view
                            calendar.gotoDate(allowedDates[0].date); // Navigate to April 2025
                        }
                    },
                },

                dateClick: function(fetchInfo) {
                    // showSchedulerModal(fetchInfo);
                },
                select: function(fetchInfo, successCallback, failureCallback){
                    if(fetchInfo) {
                        showSchedulerModal(fetchInfo, allowedDates);
                    }
                },
                eventClick: function(info,  successCallback, failureCallback) {
                    info['startStr'] = info.event.startStr;
                    info['endStr'] = info.event.endStr;

                    schedulerEventClicked(info, allowedDates);
                    info.el.style.borderColor = 'red';
                },
                eventDrop: async function(info) {
                    // Format the start and end dates to "Y-MM-DD HH:mm:ss"
                    let start = formatDateToString(info.event.start)
                    let end = formatDateToString(info.event.end)
                    let title = info.event.title;
                    let id = info.event.id;
                    let room_id = info.event._def.resourceIds[0]

                   updateCalendarEvent(id, start, end, title, room_id).then(function(response){
                        if (response.status === 'success') {
                            // Show a success message using SweetAlert
                            Swal.fire({
                                title: "Saved!",
                                text: "Session saved successfully!",
                                icon: "success"
                            });
                        } else {
                            toastr.error(response.message || "Failed to save session talks!");
                            info.revert();
                        }

                        calendar.refetchEvents();
                    })

                },
                eventResize: function(info){
                    let start = formatDateToString(info.event.start)
                    let end = formatDateToString(info.event.end)
                    let title = info.event.title;
                    let id = info.event.id;
                    let room_id = info.event._def.resourceIds[0]

                    updateCalendarEvent(id, start, end, title, room_id).then(function(response){
                        if (response.status === 'success') {
                            // Show a success message using SweetAlert
                            Swal.fire({
                                title: "Saved!",
                                text: "Session saved successfully!",
                                icon: "success"
                            });
                        } else {
                            toastr.error(response.message || "Failed to save session talks!");
                            info.revert();
                        }

                        calendar.refetchEvents();
                    })
                }

            });


            timeZoneSelectorEl.addEventListener('change', function() {
                // Update the calendar's time zone
                calendar.setOption('timeZone', this.value);
                calendar.setOption('height', '100%');

                // Optional: Refetch events to adjust to the new time zone
                calendar.refetchEvents();
            });

            calendar.render();
        })
    }

    function formatDateTime(dateString, timeString) {
        return `${dateString}T${timeString}:00Z`;
    }

    function updateCalendarEvent(id, start, end, title, room_id){
        // Send a POST request to save the changes
        return $.post(`${baseUrlAdmin}scheduler/move`, {
            start: start,
            end: end,
            title: title,
            id: id,
            room_id: room_id ?? ''
        }).done(function(response) {
            return response;
        })
            .fail(function(jqXHR, textStatus, errorThrown) {
                toastr.error("Failed to save session talks! Please try again.");
            });
    }

    $("#schedulerModal input[type='date']").flatpickr({
        mode: "range"
    });

    async function showSchedulerModal(fetchInfo, allowedDates) {
        const modal = $('#schedulerModal');

        if (fetchInfo) {
            const {
                dateStr = '',
                startStr = '',
                endStr = ''
            } = fetchInfo;
            const startDate = startStr ? new Date(startStr) : null;
            const endDate = endStr ? new Date(endStr) : null;
            const startTime = startDate ? startDate.getTime() : null;
            const endTime = endDate ? endDate.getTime() : null;

            // Setup modal content
            modal.find('.modal-title').text("Add Scheduler");
            modal.find('.modal-body').html(`<?= view('admin/scheduler/scheduler_form')?>`);
            modal.find('form').attr("id", "eventForm");
            modal.find('#updateID').val("");

            const flatpickrEnabledDates = allowedDates.map(range => {
                return {
                    from: range.startDateTime.split(" ")[0], // Extract date part only
                    to: range.endDateTime.split(" ")[0] // Extract date part only
                };
            });

            // Initialize date range picker
            flatpickr(modal.find('#floatingDay'), {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [startStr, endStr],
                enable: flatpickrEnabledDates,
                onChange: function (selectedDates) {
                    if (selectedDates.length > 0) {
                        // Extract selected start and end dates
                        const startDate = selectedDates[0];
                        const endDate = selectedDates[1] || selectedDates[0]; // Handle single date selection

                        // Call pickTime with the selected dates
                        pickTime(startDate, endDate);
                    }
                },
            });

// Initialize time pickers with default config
            const timePickerConfig = {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                minuteIncrement: 1,
                time_24hr: true,
            };


// Function to dynamically update time pickers
            function pickTime(startDate, endDate) {
                // Find allowed times for the selected start date
                const allowedDate = allowedDates.find(
                    (date) => date.date === flatpickr.formatDate(startDate, "Y-m-d")
                );

                if (allowedDate) {
                    // Extract min and max times from startDateTime and endDateTime
                    const minTime = allowedDate.startDateTime.split(" ")[1]; // Extracts "08:00:00"
                    const maxTime = allowedDate.endDateTime.split(" ")[1]; // Extracts "10:00:00"

                    // Update the time pickers dynamically
                    modal.find('#floatingTimeFrom').flatpickr({
                        ...timePickerConfig,
                        minTime: minTime || "00:00",
                        maxTime: maxTime || "23:59",
                    });

                    modal.find('#floatingTimeTo').flatpickr({
                        ...timePickerConfig,
                        minTime: minTime || "00:00",
                        maxTime: maxTime || "23:59",
                    });
                } else {
                    // If no allowedDate is found, reset to defaults
                    modal.find('#floatingTimeFrom').flatpickr({
                        ...timePickerConfig,
                        minTime: "00:00",
                        maxTime: "23:59",
                    });

                    modal.find('#floatingTimeTo').flatpickr({
                        ...timePickerConfig,
                        minTime: "00:00",
                        maxTime: "23:59",
                    });
                }
            }


            modal.find('#floatingTimeFrom').flatpickr({
                ...timePickerConfig,
                defaultDate: startTime ?? null,
            });

            modal.find('#floatingTimeTo').flatpickr({
                ...timePickerConfig,
                defaultDate: endTime ?? null,
            });

            pickTime(startDate, endDate);
            // Populate dropdowns with async data
            const populateDropdown = async (selector, data, defaultOption, selected) => {
                console.log(selected)
                const dropdown = modal.find(selector);
                dropdown.html('');
                if (defaultOption) {
                    dropdown.append(`<option value="">${defaultOption}</option>`);
                }
                data.forEach(item => {
                    const value = item.id || item.room_id || item.type; // Determine the value for the option
                    const text = (item.name && item.surname ? item.name +' '+ item.surname : item.name) || item.name || item.surname || item.type; // Determine the display text for the option
                    const isSelected = value == selected ? 'selected' : ''; // Check if this option should be selected

                    // Append the option to the dropdown
                    dropdown.append(`<option value="${value}" ${isSelected}>${text}</option>`);
                });
            };

            const populateCheckboxes = async (containerSelector, data, nameAttribute, selectedValues = []) => {
                console.log(data);
                const container = modal.find(containerSelector);
                container.html(''); // Clear existing content

                data.forEach(item => {
                    const value = item.id || item.room_id || item.type; // Determine the value for the checkbox
                    const text = (item.name && item.surname ? item.name + ' ' + item.surname : item.name) || item.name || item.surname || item.type; // Determine the label text for the checkbox
                    const isChecked = selectedValues.includes(value) ? 'checked' : ''; // Check if this checkbox should be selected

                    // Create the checkbox HTML and append it to the container
                    const checkboxHTML = `
                            <div class="form-check">
                                <input class="form-check-input form-control" type="checkbox" name="${nameAttribute}" id="${nameAttribute}-${value}" value="${value}" ${isChecked}>
                                <label class="form-check-label" for="${nameAttribute}-${value}">
                                    ${text}
                                </label>
                            </div>
                        `;
                    container.append(checkboxHTML);
                });
            };


            try {
                const [rooms, sessionChairs, sessionTypes, sessionTracks] = await Promise.all([
                    schedulerRooms(),
                    sessionChair(),
                    paperType(),
                    sessionTrack()
                ]);

                console.log(sessionTracks)
                console.log(paperType)
                if (rooms.length) {
                    populateDropdown('#floatingRooms', rooms, ' -- Select Room -- ', (fetchInfo.resource ? fetchInfo.resource._resource.id : ''));
                }

                if (sessionChairs.length) {
                    populateDropdown('.sessionChairSelect', sessionChairs, ' -- Select Chair -- ');
                }

                if (sessionTypes.length) {
                    populateDropdown('#floatingSessionType', sessionTypes, ' -- Select Session Type -- ');
                }

                if (sessionTracks.length) {
                    populateDropdown('#floatingSessionTracks', sessionTracks, ' -- Select Session Tracks -- ');
                }
            } catch (error) {
                console.error('Error loading dropdown data:', error);
                toastr.error('Failed to load dropdown options.');
            }

            // Show the modal
            modal.modal('show');
        }

        // Handle form submission
        modal.find('#eventForm').off('submit').on('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Extract values from formData
            let updateID, day, timeFrom, timeTo, sessionTitle, roomId;

            for (let [key, value] of formData.entries()) {
                if (key === 'updateID') updateID = value;
                if (key === 'day') day = value;
                if (key === 'time_from') timeFrom = value;
                if (key === 'time_to') timeTo = value;
                if (key === 'session_title') sessionTitle = value;
                if (key === 'rooms') roomId = value;
            }

            const start = `${day}T${timeFrom}`;
            const end = `${day}T${timeTo}`;

            updateCalendarEvent(updateID, start, end, sessionTitle, roomId).then(function(){
                eventCalendar.refetchEvents();
            })

            $.ajax({
                url: `${baseUrlAdmin}scheduler/create`,
                data: formData,
                processData: false,
                contentType: false,
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    if (response.status === 'success') {
                        toastr.success(response.message);
                        modal.modal('hide');
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (jqXHR) {
                    toastr.error(jqXHR.responseJSON?.message || 'An error occurred.');
                }
            });
        });
    }


    async function schedulerEventClicked(info, allowedDates) {
        return;
        $.get(baseUrlAdmin + `scheduler/get_one_json/${info.event.id}`, function(data){
            data = JSON.parse(data)
        })
        return false;
        await showSchedulerModal(info, allowedDates);
    }

    function schedulerRooms(){
        return new Promise((resolve, reject) => {
            $.post(baseUrlAdmin + 'scheduler/getAllRooms',
                function(rooms) {
                    if (rooms.length > 0) {
                        resolve(rooms);  // Pass events to the calendar
                    } else {
                        reject(new Error('No Rooms found.'));  // Error handling
                    }
                });
        });
    }

    function sessionChair(){
        return new Promise((resolve, reject) => {
            $.post(baseUrlAdmin + 'scheduler/getAllSessionChair',
                function(sessionChairs) {
                    if (sessionChairs.length > 0) {
                        resolve(sessionChairs);  // Pass events to the calendar
                    } else {
                        reject(new Error('No Session Chair found.'));  // Error handling
                    }
                });
        });
    }

    function paperType(){
        return new Promise((resolve, reject) => {
            $.post(baseUrlAdmin + 'scheduler/getAllPaperType',
                function(paperTypes) {
                    if (paperTypes.length > 0) {
                        resolve(paperTypes);  // Pass events to the calendar
                    } else {
                        reject(new Error('No Paper or Abstract found.'));  // Error handling
                    }
                });
        });
    }

    function sessionTrack(){
        return new Promise((resolve, reject) => {
            $.get(base_url + 'tracksJson',
                function(sessionTracks) {
                    if (sessionTracks.length > 0) {
                        resolve(sessionTracks);  // Pass events to the calendar
                    } else {
                        reject(new Error('No Session Track found.'));  // Error handling
                    }
                });
        });
    }

    function getDateAllowed() {
        return new Promise((resolve, reject) => {
            $.post(baseUrlAdmin + 'scheduler/getSchedulerAllowedDate', function(response) {
                let dates = [] ;
                if (response && response.length > 0) {
                    dates= response.map(function(dates){
                        return {
                            startDateTime :  dates.date_time_start,
                            endDateTime : dates.date_time_end,
                            date : dates.date,
                        }
                    })
                    resolve(dates);
                } else {
                    reject(new Error('No meeting dates found.'));
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                reject(new Error('Request failed: ' + textStatus + ' - ' + errorThrown));
            });
        });
    }


    function getScheduledEvents() {
        return new Promise((resolve, reject) => {
            $.get(baseUrlAdmin + 'scheduler/get', function(response) {
                const events = [];

                if(!response){
                    events.push({})
                }

                $.each(response, function(index, res) {
                    console.log(res)
                    if (res.presentation_date !== null) {
                        let talkTimeSummary = '';
                        let talkPresenters = [];

                        // Construct the talk summary for each talk in the session
                        let sessionChairs = '';
                        $.each(res.session_chair, function(i, chairs){
                            sessionChairs += `Moderator ${i+1}: ${chairs.name + ' ' + chairs.surname}`
                            if (chairs.acceptance) {
                                sessionChairs += `<span class="text-primary acceptanceStatus" style="display: none">
                                ${getIcon(chairs.acceptance.acceptance_confirmation)} Acceptance Confirmation
                                ${getIcon(chairs.acceptance.breakfast_attendance)} Breakfast Attendance
                                ${getIcon(chairs.acceptance.is_session_previewed)} Session Previewed
                                ${getIcon(chairs.acceptance.is_finalized)} Finalized </span>`;
                            }

                            function getIcon(value) {
                                if (value === '1' || value === 'Yes' ) return '✅';  // Checked (Accepted)
                                if (value === '2' || value === 'No') return '❌';  // Rejected
                                return '⬜';  // Incomplete
                            }
                            sessionChairs += `<br>`
                        })

                        $.each(res.talks, function(i, talk) {
                            // Format the time to exclude seconds
                            const startTime = new Date(`${talk.time_start}`).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });
                            const endTime = new Date(`${talk.time_end}`).toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: true
                            });

                            let talkCustomId = '';

                            if(talk.abstract) {
                                if(talk.abstract.submission_type == 'panel'){
                                    talkPresenters = talk.panelist.name + ' ' + talk.panelist.surname
                                    talkCustomId = 'Panelist: ' + talk.panelist.custom_id
                                }else{
                                    talkPresenters = talk.presenters.map((presenter) =>
                                        `${presenter['user_name']} ${presenter['user_surname']}`
                                    );
                                    talkPresenters = talkPresenters.join(', ');
                                    talkCustomId = 'Paper: ' + talk.abstract.custom_id
                                }
                                talkTimeSummary += `<ul class="mt-2 text-wrap"><li title="${startTime} to ${endTime} # ${talkCustomId} ${talkPresenters}">${startTime} - ${endTime} # ${talkCustomId} ${talkPresenters}</li></ul>`;
                            }else{
                                talkTimeSummary += `<ul class="mt-2 text-wrap"><li title="${startTime} to ${endTime}">${startTime} - ${endTime} : ${talk.custom_abstract_desc}</li></ul>`;
                            }

                        });

                        // Create start and end times for the event
                        let startDate = new Date(res.session_start_time); // Assuming session_date is in "YYYY-MM-DD" format
                        let endDate = new Date(res.session_end_time);   // Assuming session_date is in "YYYY-MM-DD" format

                        // Split the time part for the end time and set the correct hours
                        let [hours, minutes, seconds] = res.session_end_time.split(' ')[1].split(':');
                        endDate.setHours(hours, minutes, seconds); // Set the end time correctly

                        events.push({
                            id: res.id,
                            title: `<strong>${res.session_title}</strong><br>${getTimeOfDate(res.session_start_time)} - ${getTimeOfDate(res.session_end_time)} <br> ${sessionChairs} <br>${talkTimeSummary}`,
                            description: res.description,
                            start: startDate.toISOString(), // Convert to ISO string for FullCalendar
                            end: endDate.toISOString(),      // Convert to ISO string for FullCalendar
                            resourceId: res.rooms.room_id,   // Convert to ISO string for FullCalendar
                            extendedProps: {
                                room_id: res.rooms.room_id,
                                room_name: res.rooms.name,
                            }
                        });
                    }
                });

                if (events.length > 0) {
                    resolve(events);  // Pass events to FullCalendar
                } else {
                    reject(new Error('No abstracts found.'));  // Error handling
                }
            });
        });
    }

    function convertToISOTimeOnly(timeString) {
        const [hours, minutes, seconds] = timeString.split(':');
        const date = new Date();
        date.setHours(hours);
        date.setMinutes(minutes);
        date.setSeconds(seconds);
        return date.toISOString().split('T')[1].split('Z')[0];
    }

    function convertToISODateOnly(dateString) {
        const date = new Date(dateString);
        if (isNaN(date)) {
            throw new Error("Invalid date string"); // Handle invalid date strings
        }
        return date.toISOString().split('T')[0]; // Extracts only the date portion
    }

    function convertTimeStamp (timeStamp){
        const date = new Date(timeStamp);
        const day = date.getDay();
        const time = date.getTime();

        return day.toISOString().split('T')[0]+time.toISOString(); // Extracts only the date portion
    }


    function formatToISODateTime(dateString, timeString) {
        const date = new Date(dateString);
        const [hours, minutes, seconds] = timeString.split(':');
        date.setHours(hours);
        date.setMinutes(minutes);
        date.setSeconds(seconds);

        return date.toISOString().split('.')[0]; // Removes milliseconds if any
    }

    function editEvent(info, allowedDates){

        let schedulerModal =  $('#schedulerModal')
        schedulerModal.modal('show')
        schedulerModal.find('.modal-body').html('')
        schedulerModal.find('.modal-title').html('')


        showSchedulerModal(info, allowedDates).then(function(){
            schedulerModal.find('button[type="submit"]').text('Update');
            schedulerModal.find('#updateID').val(info.id);
            $.get(baseUrlAdmin + `scheduler/get_one_json/${info.id}`, function(data){

                let sessionChairs = '';
                if(data.scheduled_event.session_chair_ids){
                    sessionChairs = JSON.parse(data.scheduled_event.session_chair_ids);
                }

                $('#floatingDay').val(data.scheduled_event.session_day ?? '')
                $('#floatingSessionTitle').val(data.scheduled_event.session_title ?? '')
                $('#floatingSessionDescription').val(data.scheduled_event.description ?? '')
                $('#floatingSessionType').val(data.scheduled_event.paper_type ?? '')
                $('#floatingDurationTalk').val(data.scheduled_event.talk_duration ?? '')
                $('#floatingDurationBreak').val(data.scheduled_event.break_duration ?? '')
                $('#floatingSessionNumber').val(data.scheduled_event.session_number ?? '')
                $("#floatingRooms").val(data.scheduled_event.room_id ?? '').change()
                $("#floatingSessionTracks").val(data.scheduled_event.session_track ?? '').change()

                $("#floatingSessionChair1").val(sessionChairs[0] ?? '').change()
                $("#floatingSessionChair2").val(sessionChairs[1] ?? '').change()
                $("#floatingSessionChair3").val(sessionChairs[2]?? '').change()
            })
        })
        schedulerModal.find('.modal-title').html(`Manage Session #: ${info.id}`)

        //todo: update the session
    }

    // Custom function to delete event
    function talksEvent(info) {
        let schedulerModal = $('#schedulerModal');
        schedulerModal.find('.modal-footer .btn.btn-primary').hide();
        const removedAbstractIds = [];
        const talk_details = [];
        let talkDetail = {};
        $.get(`${baseUrlAdmin}scheduler/render_talks/${info.id}`, function(response) {
            if (!response) return;

            const events = [];
            schedulerModal.modal('show');
            schedulerModal.find('.modal-title').html(`<p>Assigning Talks to: #${info.id}</p>`);
            schedulerModal.find('.modal-body').html(response);
            schedulerModal.find('.modal-footer .btn .btn-primary').attr('id', 'save-session-talks');

            let tableAbstract = schedulerModal.find('#abstractTable');
            let tableAddedAbstract = schedulerModal.find('#tableAddedAbstract');

            // Get session parameters
            let sessionDuration = schedulerModal.find('.session-duration').data('value');
            let sessionDate = schedulerModal.find('.session-date').data('value');
            let talkDuration = schedulerModal.find('.session-talk-duration').data('value');
            let breakDuration = schedulerModal.find('.session-break-duration').data('value');

            let tableAddedAbstractArray = [];
            let durationInMinutes = talkDuration;

            let removedAddedTalksIds = [];

            // Add Abstract Button Click Handler
            schedulerModal.find('#addAbstractBtn').off('click').on('click', function(e) {
                e.preventDefault();
                const abstract_ids = [];

                const updatedAddedAbstractTotalDuration = 0;

                // Collect selected abstracts
                tableAbstract.find(".row-select:checked").each(function() {
                    abstract_ids.push($(this).data('abstract-id'));
                });

                // Fetch and add abstracts
                getAbstract(abstract_ids, function(data) {
                    // removedAddedTalksIds.filter((removedAddedTalksId) => abstract_ids.includes(removedAddedTalksId));

                    if (abstract_ids.length > 0) {
                        $.each(abstract_ids, function(i, abstract_id) {
                            const index = removedAddedTalksIds.indexOf(abstract_id);
                            if (index !== -1) {
                                removedAddedTalksIds.splice(index, 1); // Remove the element at the found index
                            }
                        });
                    }

                    if (!data) return;

                    let startTime = new Date(info.startStr);
                    let startDate = new Date(info.startStr);
                    startDate = startDate.getDate();


                    data.forEach((res, i) => {

                        let presenters = getPresenters(res.authors);
                        console.log(presenters)
                        let endTime = addDuration(startTime, (durationInMinutes + breakDuration));

                        // Validate end time
                        if (new Date(info.endStr) <= endTime) {
                            toastr.error("Time already exceeded!");
                        }

                        let formattedStartTime = getTimeOfDate(startTime);
                        let formattedEndTime = getTimeOfDate(endTime);

                        talkDetail = {
                            abstract_id: res.paper.id,
                            session_date: sessionDate,
                            custom_id: res.paper.custom_id,
                            duration: durationInMinutes,
                            start_time: formattedStartTime,
                            end_time: formattedEndTime,
                            presenters: presenters,
                            break_duration: breakDuration
                        };

                        if (!talk_details.some(detail => detail.abstract_id === res.paper.id)) {
                            talk_details.push(talkDetail);
                        }

                        // Append talk row

                        if (res.paper.submission_type == 'panel') {
                            createPanelTalkRows(res.paper, talkDetail, presenters, formattedStartTime, formattedEndTime)
                                .then(function() {
                                    console.log(getTimeOfDate(new Date(info.startStr)))
                                    // Only call updateTalkDuration after the rows are created
                                    updateTalkDuration(getTimeOfDate(new Date(info.startStr)));
                                })
                                .catch(function(error) {
                                    console.error('Error in creating talk rows:', error);
                                });
                        } else {
                            tableAddedAbstract.find('tbody').append(createTalkRow(res.paper, talkDetail, presenters, formattedStartTime, formattedEndTime));
                            updateTalkDuration(getTimeOfDate(new Date(info.startStr)));
                        }

                        tableAddedAbstractArray.push({
                            'id' : res.paper.id,
                            'paper' : res.paper,
                            'talks' : talkDetail,
                            'presenters' :  presenters,
                            'formattedStartTime' : formattedStartTime, 'formattedEndTime' : formattedEndTime
                        });

                        tableAbstract.find(`[data-abstract-id="${res.paper.id}"]`).closest('tr').hide().find('input[type="checkbox"]').prop('checked', false);

                        removedAbstractIds.shift(res.paper.id);
                        startTime = endTime;
                    });
                });



                // Duration Change Handler
                tableAddedAbstract.off('change input', '.talk-duration').on('change input', '.talk-duration', function() {
                    updateTalkDuration(getTimeOfDate( new Date(info.startStr)));
                });

                // Remove Abstract Handler
                tableAddedAbstract.off('click', '.remove').on('click', '.remove', function(e) {
                    e.preventDefault();
                    let abstractAddedId = $(this).data('abstract-id');

                    // Update tableAddedAbstractArray by removing the matching abstract ID
                    tableAddedAbstractArray = tableAddedAbstractArray.filter(item => item['abstract_id'] !== abstractAddedId);


                    // Add to removedAbstractIds if not already present
                    if (!removedAbstractIds.includes(abstractAddedId)) {
                        removedAbstractIds.push(abstractAddedId);
                    }

                    // Show the row back in the original table and remove it from the added table
                    tableAbstract.find(`[data-abstract-id="${abstractAddedId}"]`).closest('tr').show();
                    tableAddedAbstract.find(`tr[id="${abstractAddedId}"]`).remove();
                    $(this).closest('tr').remove();

                    // Trigger change on talk-duration and push to removedAddedTalksIds
                    tableAddedAbstract.find('.talk-duration').change();
                    removedAddedTalksIds.push(abstractAddedId);

                    // Update the talk duration
                    updateTalkDuration(getTimeOfDate(new Date(info.startStr)));
                });



                // Save Session Talks
                schedulerModal.find('#save-session-talks').off('click').on('click', function() {
                    // console.log(sessionDuration)

                    let talksTable = $('#tableAddedAbstract')
                    let talk_duration = 0;
                    let added_talk_details = [];
                    talksTable.find('tr').each(function(){
                        let abstract_id = $(this).attr('id')
                        if(abstract_id) {
                            talk_duration = $(this).find('.talk-duration').val()
                            let start_time = $(this).find('.start-time').text() ?? ''
                            let end_time = $(this).find('.end-time').text() ?? ''
                            talk_duration = $(this).find('.talk-duration').val()?? ''
                            let custom_desc = $(this).find('#talk_custom_desc').val() ?? '';
                            let paper_sub_id = $(this).data('paper-sub-id')
                            added_talk_details.push(
                                {
                                    'duration': talk_duration,
                                    'start_time': start_time,
                                    'end_time': end_time,
                                    'abstract_id': abstract_id,
                                    'break_duration':$(".session-break-duration").data('value'),
                                    'scheduler_event_id': info.id,
                                    'custom_desc': custom_desc,
                                    'paper_sub_id': paper_sub_id,
                                })
                        }
                    })

                    if (getTotalDuration(tableAddedAbstract, breakDuration ?? 0) > sessionDuration) {
                        Swal.fire({
                            title: "Are you sure?",
                            text: "Total of talk duration exceeds the session duration!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes, save it!"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                if (added_talk_details.length > 0) {
                                    saveTalks(added_talk_details, removedAddedTalksIds);
                                } else {
                                    toastr.info("No valid talks to save after filtering.");
                                }
                            }
                        });
                        return false;
                    }else{
                        saveTalks(added_talk_details, removedAddedTalksIds);
                    }
                });

            });

            schedulerModal.find('#addCustomEventBtn').off('click').on('click', function(e) {
                let tableAddedAbstract = $("#tableAddedAbstract")
                let customEventCount = 0;
                let initialCustomEventCount = 0;
                tableAddedAbstract.find('.customAddedEvent').each(function(){
                    initialCustomEventCount++;
                })

                customEventCount = initialCustomEventCount + customEventCount;

                customEventCount ++ ;
                tableAddedAbstract.find('tbody').append(
                        `<tr id="custom_${customEventCount}" class="customAddedEvent">
                        <td><span class="start-time"></span> - <span class="end-time"></span></td>
                        <td><input type="number" class="talk-duration" style="width:50px" value="${talkDuration}"></td>
                        <td class="text-nowrap "></td>
                        <td><input type="text" name="talk_custom_desc" id="talk_custom_desc"></td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm moveUp" onclick="moveUp(this)" data-abstract-id="custom_${customEventCount}"  data-initial-time="${getTimeOfDate(new Date(info.startStr))}"><i class="fas fa-arrow-up"></i></a>
                                <a class="btn btn-sm moveDown" onclick="moveDown(this)" data-abstract-id="custom_${customEventCount}"  data-initial-time="${getTimeOfDate(new Date(info.startStr))}"><i class="fas fa-arrow-down"></i></a>
                                <a class="btn btn-sm remove" data-abstract-id="custom_${customEventCount}"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>`
                );
                updateTalkDuration(getTimeOfDate(new Date(info.startStr)))
            })

            getTalks(talk_details, talkDetail, info);

            function checkExistingAddedTalks(tableAddedAbstract){
                tableAddedAbstract.find('tr').each(function(){
                    alert()
                })

            }

            function saveTalks(added_talk_details, removedAddedTalksIds) {
                // Validate inputs
                if (!Array.isArray(added_talk_details)) {
                    console.error("Invalid data format for talks.");
                    toastr.error("Invalid data provided. Please try again.");
                    return;
                }

                // Send POST request
                $.post(`${baseUrlAdmin}talks/create`, {
                    talk_details: added_talk_details,
                    removed_talks: removedAddedTalksIds,
                    scheduler_event_id: info.id
                }, function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: "Saved!",
                            text: "Session saved successfully!",
                            icon: "success"
                        });

                        eventCalendar.refetchEvents();
                    } else {
                        toastr.error(response.message || "Failed to save session talks!");
                    }
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    console.error(`Error: ${textStatus}, Details: ${errorThrown}`);
                    toastr.error("Failed to save session talks! Please try again.");
                });
            }

            function updateTalkDuration(startTime) {
                let tableAddedAbstract = $("#tableAddedAbstract");
                let rows = tableAddedAbstract.find(`tbody tr`);
                let breakDuration = schedulerModal.find('.session-break-duration').data('value');
                let currentStartTime = startTime;
                let duration = 0;
                let endTime = 0;

                rows.each(function (index) {
                    let row = $(this);
                    let durationInput = row.find('.talk-duration');
                    let endTimeElement = row.find('.end-time');

                    if (index > 0) {
                        endTime = addTimeDuration(endTime, breakDuration);
                    }

                    duration = parseFloat(durationInput.val());
                    endTime = addTimeDuration(currentStartTime, duration);
                    endTime = addTimeDuration(endTime, breakDuration);
                    // endTime = addTimeDuration(endTime, breakDuration);

                    row.find('.start-time').text(currentStartTime);
                    row.find('.end-time').text(endTime);
                    endTimeElement.text(endTime);

                    currentStartTime = endTime;
                });
            }

        });


        // Helper functions
        function getPresenters(authors) {
            return (authors || []).filter(author => author.is_presenting_author === "Yes")
                .map(author => `${author.details.name} ${author.details.surname}`)
                .join('<br>');
        }


        function addDuration(start, minutes = 0) {
            let newTime = new Date(start);
            newTime.setMinutes(newTime.getMinutes() + minutes);
            newTime.setSeconds(0);
            return newTime;
        }

        function addTimeDuration(startTime, duration) {
            let [startHours, startMinutes] = startTime.split(':').map(parseFloat);
            let totalMinutes = startMinutes + duration;
            let endHours = startHours + Math.floor(totalMinutes / 60);
            let endMinutes = totalMinutes % 60;
            return `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
        }


        function createTalkRow(paper, talkDetail, presenters, startTime, endTime) {
            let row = $(`<tr id="${paper.id}">`);
            row.append(`<td><span class="start-time"></span> - <span class="end-time"></span></td>`);
            row.append(`<td><input type="number" class="talk-duration" style="width:50px" data-abstract-id="${paper.id}" value="${talkDetail.duration}"></td>`);
            row.append(`<td class="text-nowrap ">${presenters}</td>`);
            row.append(`<td>Abstract ID: (<span class="fw-bold">${paper.custom_id})</span><br> ${stripTags(paper.title)}</td>`);
            row.append(`
            <td class="text-nowrap">
                <a class="btn btn-sm" onclick="moveUp(this)" data-abstract-id="${paper.id}" data-initial-time="${getHourMin(startTime)}"><i class="fas fa-arrow-up"></i></a>
                <a class="btn btn-sm" onclick="moveDown(this)" data-abstract-id="${paper.id}" data-initial-time="${getHourMin(startTime)}"><i class="fas fa-arrow-down"></i></a>
                <a class="btn btn-sm remove" data-abstract-id="${paper.id}"><i class="fas fa-trash"></i></a>
            </td>
        `);
            return row;
        }


        async function createPanelTalkRows(paper, talkDetail, presenters, startTime, endTime) {
            return new Promise((resolve, reject) => {
                const rows = [];
                getPanelsAbstract([paper.id]).then(function(panels) { // If abstract is panel make it show all the panelist and if one panelist is deleted all panelist should be deleted as well.
                    if (panels) {
                        $.each(panels.data[0].panelist_abstract, function(i, panel) {
                            let row = $(`<tr id="${panel.paper_id}" data-paper-sub-id="${panel.individual_panel_id}" >`); // Use a unique id per row based on index
                            row.append(`<td><span class="start-time"></span> - <span class="end-time"></span></td>`);
                            row.append(`<td><input type="number" class="talk-duration" style="width:50px" data-abstract-id="${paper.id}" value="${talkDetail.duration}"></td>`);
                            row.append(`<td class="text-nowrap">${panel.name + ' '+ panel.surname}</td>`);
                            row.append(`<td>Abstract ID: (<span class="fw-bold">${panel.custom_id})</span><br> ${stripTags(panel.individual_panel_title)}</td>`);
                            row.append(`
                        <td class="text-nowrap">
                            <a class="btn btn-sm" onclick="moveUp(this)" data-abstract-id="${paper.id}" data-initial-time="${getHourMin(startTime)}"><i class="fas fa-arrow-up"></i></a>
                            <a class="btn btn-sm" onclick="moveDown(this)" data-abstract-id="${paper.id}" data-initial-time="${getHourMin(startTime)}"><i class="fas fa-arrow-down"></i></a>
                            <a class="btn btn-sm remove" data-abstract-id="${paper.id}"><i class="fas fa-trash"></i></a>
                        </td>`);

                            rows.push(row); // Add this row to the rows array
                        });

                        // Append all rows at once after the loop completes
                        rows.forEach(function(row) {
                            $('#tableAddedAbstract').find('tbody').append(row); // Assuming you're appending to a table with id 'table-id'
                        });

                        resolve(); // Resolve the promise after appending all rows
                    } else {
                        reject('No panels found');
                    }
                }).catch(function(error) {
                    reject(error); // Reject the promise if getPanelsAbstract fails
                });
            });
        }


        function getTotalDuration(table, breakDuration) {
            let totalDuration = 0;
            table.find('.talk-duration').each(function() {
                totalDuration += breakDuration + (parseInt($(this).val(), 10) || 0);
            });

            // console.log(totalDuration);
            return totalDuration;
        }

    }

        async function getPanelsAbstract(abstract_panel_ids){
            return $.post(`${baseUrlAdmin}/getAllPanelsWithId`, {
                abstract_panel_ids: abstract_panel_ids,
                submission_type:'panel',
            }, function(response) {
                if(response.status == 'success') {
                    return response.data
                }
            })
        }

    function deleteEvent(info) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(baseUrlAdmin + `scheduler/delete/${info.id}`, function(data) {
                    if (data) {
                        if(data.status == 'success'){
                            Swal.fire({
                                title: "Deleted!",
                                text: "Your file has been deleted.",
                                icon: "success"
                            });
                        }
                    }
                    eventCalendar.refetchEvents();
                });
            }
        });
    }

    // For adding Talks
    function getAbstract(abstract_ids, callback) {

        abstract_ids = JSON.stringify(abstract_ids);
        $.get(baseUrlAdmin + `scheduler/get_scheduled_events/${abstract_ids}`, function(data) {
            if (data) {
                callback(data);
            }
        });
    }


    function stripTags(input) {
        return $("<div>").html(input).text();
    }

    function addDuration(date, hours = 0, minutes = 0, seconds = 0) {
        let newDate = new Date(date); // Create a new Date object to avoid modifying the original date
        newDate.setHours(newDate.getHours() + hours);
        newDate.setMinutes(newDate.getMinutes() + minutes);
        newDate.setSeconds(newDate.getSeconds() + seconds);
        return newDate;
    }



    function getTalks(talk_details, talkDetail, info) {
        $.get(`${baseUrlAdmin}/talks/scheduled/${info.id}`, function (response) {
            $("#tableAddedAbstract").find('tbody').html('');
            if (response.status == 'success') {
                $.each(response.data, function (i, data) {
                    if (data.scheduler_event_id && data.scheduler_event_id == info.id) {
                        let presentersList = '';
                        let display_id = '';
                        let customAbstractTitle = '';

                        if (data.submission_type === 'panel' && data.scheduler_event_id == info.id) {
                            // Handle panel submission type
                            if (data.panelist) {
                                presentersList = `${data.panelist.name} ${data.panelist.surname}<br>`;
                                display_id = data.paper_sub.custom_id || '';
                                customAbstractTitle = data.paper_sub.individual_panel_title || '';
                            }
                        } else {
                            // Handle other types (e.g., 'paper')
                            if (data.presenters.length > 0) {
                                $.each(data.presenters, function (j, presenter) {
                                    presentersList += `${presenter.user_name} ${presenter.user_surname}<br>`;
                                });
                            }
                            display_id = data.abstract_custom_id || '';
                            customAbstractTitle = data.abstract_title ? stripTags(data.abstract_title) : data.custom_abstract_desc || '';
                        }


                        const startTime = getTimeOfDate(data.time_start)
                        const endTime = getTimeOfDate(data.time_end)

                        if (data.schedule && data.schedule.length > 0) {
                            $.each(data.schedule, function (j, res) {
                                talkDetail = {
                                    abstract_id: res.abstract_id,
                                    session_date: sessionDate,
                                    duration: data.duration,
                                    start_time: data.time_start,
                                    end_time: data.time_end,
                                    presenters: presentersList,
                                    break_duration: data.break_duration
                                };
                            });
                        }

                        // Append data to table
                        $("#tableAddedAbstract").find('tbody').append(
                            `<tr id="${data.abstract_id}" data-paper-sub-id="${data.paper_sub_id}">
                            <td><span class="start-time">${startTime}</span> - <span class="end-time">${endTime}</span></td>
                            <td><input type="number" class="talk-duration" style="width:50px" data-abstract-id="${data.abstract_id}" value="${data.duration}"></td>
                            <td class="text-nowrap">${presentersList || ''}</td>
                            <td>
                                ${data.abstract_custom_id ? `
                                    Abstract ID: <span class="fw-bold">(${display_id})</span><br>
                                    ${customAbstractTitle}
                                ` : `
                                    <input type="text" name="talk_custom_desc" id="talk_custom_desc" value="${data.custom_abstract_desc || ''}">
                                `}
                            </td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm moveUp" onclick="moveUp(this)" data-abstract-id="${data.abstract_id}" data-initial-time="${response.data[0].time_start}"><i class="fas fa-arrow-up"></i></a>
                                <a class="btn btn-sm moveDown" onclick="moveDown(this)" data-abstract-id="${data.abstract_id}" data-initial-time="${response.data[0].time_start}"><i class="fas fa-arrow-down"></i></a>
                                <a class="btn btn-sm remove" data-abstract-id="${data.abstract_id}"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>`
                        );
                    }
                });
                $('#addAbstractBtn').trigger('click');
            }
        }).fail(function () {
            toastr.error("Failed to fetch session talks!");
        });
    }

    function get12hrs(date){
        return new Date(`${date}`).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    function formatDateToString(date) {
        let year = date.getFullYear();
        let month = ('0' + (date.getMonth() + 1)).slice(-2); // Months are 0-indexed
        let day = ('0' + date.getDate()).slice(-2);
        let hours = ('0' + date.getHours()).slice(-2);
        let minutes = ('0' + date.getMinutes()).slice(-2);
        let seconds = ('0' + date.getSeconds()).slice(-2);

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    function getTimeOfDate(dateStr){
        return new Date(`${dateStr}`).toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    function getHourMin(time){
        return time.split(':').slice(0, 2).join(':');
    }

    function moveDown(element) {
        const row = $(element).closest('tr');
        const nextRow = row.next();

        const initialTime = element.dataset.initialTime; // Use the initial time provided
        if (nextRow.length) {
            // Swap rows in the DOM
            row.insertAfter(nextRow);

            // Adjust times after moving
            updateTalkDurations(initialTime);
        } else {
            toastr.info('This is already the last row.');
        }
    }

    function moveUp(element) {
        const row = $(element).closest('tr');
        const prevRow = row.prev();

        const initialTime = element.dataset.initialTime; // Use the initial time provided
        if (prevRow.length) {
            // Swap rows in the DOM
            row.insertBefore(prevRow);

            // Adjust times after moving
            updateTalkDurations(initialTime);
        } else {
            toastr.info('This is already the first row.');
        }
    }

    function updateTalkDurations(initialTime) {
        const table = $('#tableAddedAbstract tbody');
        const rows = table.find('tr');
        let currentTime = getHourMin(initialTime); // Ensure the initial time is used for the first row
        const breakDuration = parseFloat($('.session-break-duration').data('value')) || 0; // Break duration in minutes
        console.log('break:' + breakDuration)
        rows.each(function (index) {
            const row = $(this);
            const duration = parseFloat(row.find('.talk-duration').val()) || 0; // Get the talk duration
            let endTime = addMinutesToTime(currentTime, duration);
            row.find('.start-time').text(currentTime);
            currentTime = addMinutesToTime(endTime, breakDuration);
            endTime = addTimeDuration(endTime, breakDuration);
            row.find('.end-time').text(endTime);
        });
    }

    function addMinutesToTime(startTime, minutesToAdd) {
        const [hours, minutes, sec] = startTime.split(':').map(Number);
        const totalMinutes = hours * 60 + minutes + minutesToAdd;
        const newHours = Math.floor(totalMinutes / 60) % 24;
        const newMinutes = totalMinutes % 60;
        return `${String(newHours).padStart(2, '0')}:${String(newMinutes).padStart(2, '0')}`;
    }

    function addTimeDuration(startTime, duration) {
        let [startHours, startMinutes] = startTime.split(':').map(parseFloat);
        let totalMinutes = startMinutes + duration;
        let endHours = startHours + Math.floor(totalMinutes / 60);
        let endMinutes = totalMinutes % 60;
        return `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
    }

</script>


<script>
    // Function to open the side nav
    function toggleNav() {
        const sideNav = document.getElementById("sidenav");
        const mainContent = document.getElementById("calendar");
        const abstract_list = document.getElementById("abstract_list");

        // Toggle the "open" class to both elements
        sideNav.classList.toggle("open");
        mainContent.classList.toggle("open");
        abstract_list.classList.toggle("open");
    }
</script>