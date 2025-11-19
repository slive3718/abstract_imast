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
        color: #000000 ;
        overflow-x: auto;
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
        font-size:14px
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

    /* Adjust main content when the nav is open */
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
    let baseUrlAdmin = "<?=base_url('admin/')?>";
    let eventCalendar;
    let allowedDates = []; // Global for allowed dates

    document.addEventListener('DOMContentLoaded', function() {
        renderCalendar();
    });

    function renderCalendar() {
        getDateAllowed().then(function(allowed) {
            allowedDates = allowed; // Set global
            var timeZoneSelectorEl = document.getElementById('time-zone-selector');
            var calendarEl = document.getElementById('calendar');
            eventCalendar = new FullCalendar.Calendar(calendarEl, {
                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                timeZone: 'local',
                initialView: 'roomView',
                navLinks: true,
                editable: true,
                selectable: true,
                height: 'auto',
                initialDate: allowed[0]?.date ?? '2025-02-05',
                headerToolbar: {
                    left: 'customDays roomView prev,next',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                expandRows: true,
                contentHeight: 'auto',
                resources: async function() {
                    let rooms = await schedulerRooms();
                    return rooms.map(room => ({
                        id: room.room_id,
                        title: room.name
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
                    const dateStr = info.date.toISOString().split('T')[0];
                    const isAllowed = allowedDates.some(range => {
                        const rangeStartDate = new Date(range.startDateTime).toISOString().split('T')[0];
                        const rangeEndDate = new Date(range.endDateTime).toISOString().split('T')[0];
                        return dateStr >= rangeStartDate && dateStr <= rangeEndDate;
                    });
                    if (!isAllowed) {
                        info.el.classList.add('fc-disabled-day');
                    }
                },
                eventContent: createEventContent,
                events: function(fetchInfo, successCallback, failureCallback) {
                    getScheduledEvents()
                        .then(function(events) {
                            successCallback(events);
                        })
                        .catch(function(error) {
                            failureCallback(error);
                        });
                },
                datesSet: function(info) {
                    const currentDate = info.view.currentStart.toISOString().split('T')[0];
                    const customTimeSlot = allowedDates.find(entry => entry.date === currentDate);
                    if (customTimeSlot) {
                        const startTime = customTimeSlot.startDateTime.split(' ')[1];
                        const endTime = customTimeSlot.endDateTime.split(' ')[1];
                        eventCalendar.setOption('slotMinTime', startTime);
                        eventCalendar.setOption('slotMaxTime', endTime);
                    } else {
                        eventCalendar.setOption('slotMinTime', '00:00:00');
                        eventCalendar.setOption('slotMaxTime', '24:00:00');
                    }
                },
                views: {
                    timeGridDay: {
                        slotDuration: '00:05:00',
                        slotLabelInterval: 1,
                    },
                    listWeek: {
                        eventDidMount: function(info) {
                            const timeElement = info.el.querySelector(".fc-list-event-time");
                            if (timeElement) {
                                const customDiv = document.createElement("div");
                                customDiv.textContent = info.event.extendedProps.room_name;
                                timeElement.appendChild(customDiv);
                            }
                            const acceptanceElements = info.el.querySelectorAll('.acceptanceStatus');
                            acceptanceElements.forEach(el => {
                                el.style.display = 'block';
                            });
                        },
                        events: function(events) {
                            return events.sort((a, b) => {
                                if (a.extendedProps.room_name < b.extendedProps.room_name) return -1;
                                if (a.extendedProps.room_name > b.extendedProps.room_name) return 1;
                                return 0;
                            });
                        },
                        eventContent: createEventContent,
                    },
                    timeGridWeek: {
                        slotDuration: '00:15:00',
                        slotLabelInterval: 1,
                    },
                    dayGridMonth: {
                        slotDuration: '00:15:00',
                        slotLabelInterval: 1,
                    },
                    customDays: {
                        slotDuration: '00:05:00',
                        slotLabelInterval: 1,
                    },
                    roomView: {
                        height: 100,
                        type: 'resourceTimeline',
                        duration: { days: 1 },
                        slotDuration: '00:15:00',
                        slotLabelInterval: "00:15",
                        expandRows: true,
                        overlap: false,
                    },
                    customWeek: {
                        type: 'resourceTimelineDay',
                        duration: { days: 4 },
                        visibleRange: function (currentDate) {
                            let start = new Date(currentDate);
                            start.setDate(start.getDate() - start.getDay() + 6);
                            let end = new Date(start);
                            end.setDate(start.getDate() + 3);
                            return { start, end };
                        },
                        dayHeaderFormat: { weekday: 'short', day: '2-digit' },
                        slotDuration: '00:05:00',
                        slotLabelInterval: 1,
                        datesSet: function(info) {
                            eventCalendar.setOption('slotMinTime', '00:00:00');
                            eventCalendar.setOption('slotMaxTime', '24:00:00');
                        },
                    }
                },
                customButtons: {
                    customDays: {
                        text: 'Meeting Dates',
                        click: function () {
                            eventCalendar.changeView('customWeek');
                            eventCalendar.gotoDate(allowedDates[0]?.date);
                        }
                    },
                    roomView: {
                        text: 'Day View',
                        click: function () {
                            eventCalendar.changeView('roomView');
                            eventCalendar.gotoDate(allowedDates[0]?.date);
                        }
                    },
                },
                dateClick: function(fetchInfo) {
                    // showSchedulerModal(fetchInfo);
                },
                select: function(fetchInfo) {
                    if (fetchInfo) {
                        showSchedulerModal(fetchInfo, allowedDates);
                    }
                },
                eventClick: function(info) {
                    info.startStr = info.event.startStr;
                    info.endStr = info.event.endStr;
                    schedulerEventClicked(info, allowedDates);
                    info.el.style.borderColor = 'red';
                },
                eventDrop: handleEventUpdate,
                eventResize: handleEventUpdate
            });

            timeZoneSelectorEl.addEventListener('change', function() {
                eventCalendar.setOption('timeZone', this.value);
                eventCalendar.setOption('height', '100%');
                eventCalendar.refetchEvents();
            });

            eventCalendar.render();
        }).catch(error => {
            console.error('Failed to load allowed dates:', error);
        });
    }

    // Reusable function to create event content
    function createEventContent(arg) {
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

        // Delete button (uncommented and consistent)
        let deleteButton = document.createElement('button');
        deleteButton.innerHTML = 'Delete';
        deleteButton.className = 'event-delete-button';
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

    // Reusable event update handler
    async function handleEventUpdate(info) {
        let start = formatDateToString(info.event.start);
        let end = formatDateToString(info.event.end);
        let title = info.event.title;
        let id = info.event.id;
        let room_id = info.event._def.resourceIds[0];

        try {
            let response = await updateCalendarEvent(id, start, end, title, room_id);
            if (response.status === 'success') {
                Swal.fire({
                    title: "Saved!",
                    text: "Session saved successfully!",
                    icon: "success"
                });
            } else {
                toastr.error(response.message || "Failed to save session talks!");
                info.revert();
            }
            eventCalendar.refetchEvents();
        } catch (error) {
            toastr.error("Failed to save session talks! Please try again.");
            info.revert();
        }
    }

    function formatDateTime(dateString, timeString) {
        return `${dateString}T${timeString}:00Z`;
    }

    function updateCalendarEvent(id, start, end, title, room_id) {
        return $.post(`${baseUrlAdmin}scheduler/move`, {
            start: start,
            end: end,
            title: title,
            id: id,
            room_id: room_id ?? ''
        }).done(function(response) {
            return response;
        }).fail(function(jqXHR, textStatus, errorThrown) {
            toastr.error("Failed to save session talks! Please try again.");
            throw new Error(textStatus);
        });
    }

    $("#schedulerModal input[type='date']").flatpickr({
        mode: "range"
    });

    async function showSchedulerModal(fetchInfo, allowed) {
        const modal = $('#schedulerModal');

        if (fetchInfo) {
            const { dateStr = '', startStr = '', endStr = '' } = fetchInfo;
            const startDate = startStr ? new Date(startStr) : null;
            const endDate = endStr ? new Date(endStr) : null;
            const startTime = startDate ? startDate.getTime() : null;
            const endTime = endDate ? endDate.getTime() : null;

            // Setup modal content
            modal.find('.modal-title').text("Add Scheduler");
            modal.find('.modal-body').html(`<?= view('admin/scheduler/scheduler_form')?>`);
            modal.find('form').attr("id", "eventForm");
            modal.find('#updateID').val("");

            const flatpickrEnabledDates = allowed.map(range => ({
                from: range.startDateTime.split(" ")[0],
                to: range.endDateTime.split(" ")[0]
            }));

            // Initialize date range picker
            flatpickr(modal.find('#floatingDay'), {
                mode: "range",
                dateFormat: "Y-m-d",
                defaultDate: [startStr, endStr],
                enable: flatpickrEnabledDates,
                onChange: function (selectedDates) {
                    if (selectedDates.length > 0) {
                        const startDate = selectedDates[0];
                        const endDate = selectedDates[1] || selectedDates[0];
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
                const allowedDate = allowed.find(
                    (date) => date.date === flatpickr.formatDate(startDate, "Y-m-d")
                );

                if (allowedDate) {
                    const minTime = allowedDate.startDateTime.split(" ")[1];
                    const maxTime = allowedDate.endDateTime.split(" ")[1];

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
                const dropdown = modal.find(selector);
                dropdown.html('');
                if (defaultOption) {
                    dropdown.append(`<option value="">${defaultOption}</option>`);
                }
                data.forEach(item => {
                    const value = item.id || item.room_id || item.type;
                    const text = (item.name && item.surname ? item.name +' '+ item.surname : item.name) || item.name || item.surname || item.type;
                    const isSelected = value == selected ? 'selected' : '';

                    dropdown.append(`<option value="${value}" ${isSelected}>${text}</option>`);
                });
            };

            const populateCheckboxes = async (containerSelector, data, nameAttribute, selectedValues = []) => {
                const container = modal.find(containerSelector);
                container.html('');

                data.forEach(item => {
                    const value = item.id || item.room_id || item.type;
                    const text = (item.name && item.surname ? item.name + ' ' + item.surname : item.name) || item.name || item.surname || item.type;
                    const isChecked = selectedValues.includes(value) ? 'checked' : '';

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

                if (rooms.length) {
                    populateDropdown('#floatingRooms', rooms, ' -- Select Room -- ', (fetchInfo.resource ? fetchInfo.resource._resource.id : ''));
                }

                if (sessionChairs.length) {
                    populateDropdown('.sessionChairSelect', sessionChairs, ' -- Select Moderator -- ');
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
            });

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

    async function schedulerEventClicked(info, allowed) {
        // Placeholder - can be expanded
        return;
    }

    function schedulerRooms() {
        return new Promise((resolve, reject) => {
            $.post(baseUrlAdmin + 'scheduler/getAllRooms', function(rooms) {
                if (rooms.length > 0) {
                    resolve(rooms);
                } else {
                    reject(new Error('No Rooms found.'));
                }
            }).fail(reject);
        });
    }

    function sessionChair() {
        return new Promise((resolve, reject) => {
            $.post(baseUrlAdmin + 'scheduler/getAllSessionChair', function(sessionChairs) {
                console.log(sessionChairs)
                if (sessionChairs.length > 0) {
                    resolve(sessionChairs);
                } else {
                    reject(new Error('No Session Chair found.'));
                }
            }).fail(reject);
        });
    }

    function paperType() {
        return new Promise((resolve, reject) => {
            $.post(baseUrlAdmin + 'scheduler/getAllPaperType', function(paperTypes) {
                if (paperTypes.length > 0) {
                    resolve(paperTypes);
                } else {
                    reject(new Error('No Paper or Abstract found.'));
                }
            }).fail(reject);
        });
    }

    function sessionTrack() {
        return new Promise((resolve, reject) => {
            $.get(base_url + 'tracksJson', function(sessionTracks) {
                if (sessionTracks.length > 0) {
                    resolve(sessionTracks);
                } else {
                    reject(new Error('No Session Track found.'));
                }
            }).fail(reject);
        });
    }

    function getDateAllowed() {
        return new Promise((resolve, reject) => {
            $.post(baseUrlAdmin + 'scheduler/getSchedulerAllowedDate', function(response) {
                let dates = [];
                if (response && response.length > 0) {
                    dates = response.map(function(item) {
                        return {
                            startDateTime: item.date_time_start,
                            endDateTime: item.date_time_end,
                            date: item.date,
                        };
                    });
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

                if (!response || response.length === 0) {
                    resolve(events); // Return empty array instead of pushing empty object
                    return;
                }

                $.each(response, function(index, res) {
                    if (res.presentation_date !== null) {
                        let talkTimeSummary = '';
                        let sessionChairs = '';

                        $.each(res.session_chair, function(i, chairs) {
                            sessionChairs += `Moderator ${i+1}: ${chairs.name + ' ' + chairs.surname}`;
                            if (chairs.acceptance) {
                                sessionChairs += `<span class="text-primary acceptanceStatus" style="display: none">
                                    ${getIcon(chairs.acceptance.acceptance_confirmation)} Acceptance Confirmation
                                    ${getIcon(chairs.acceptance.is_finalized)} Finalized </span>`;
                            }
                            sessionChairs += `<br>`;
                        });

                        $.each(res.talks, function(i, talk) {
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
                            let talkPresenters = [];

                            if (talk.abstract) {
                                console.log(talk)
                                if (talk.abstract.submission_type == 'panel') {
                                    talkPresenters = talk.panelist.name + ' ' + talk.panelist.surname;
                                    talkCustomId = 'Panelist: ' + talk.panelist.custom_id;
                                } else {
                                    talkPresenters = talk.presenters
                                        .filter(presenter => presenter.is_presenting_author === 'Yes')
                                        .map((presenter) => {
                                            const name = `${presenter.user_name} ${presenter.user_surname}`;
                                            const acceptance = getIcon(presenter.acceptance.acceptance_confirmation);
                                            const travel = getIcon(presenter.acceptance.travel_expenses);
                                            const celebration = getIcon(presenter.acceptance.celebration_attendance);

                                            return `${name} | ${acceptance} Acceptance | ${travel} T&E | ${celebration} Celebration`;
                                        });
                                    talkPresenters = talkPresenters.join(', ');
                                    talkCustomId = 'Paper: ' + talk.abstract.custom_id;
                                }
                                talkTimeSummary += `<ul class="mt-2 text-wrap"><li title="${startTime} to ${endTime} # ${talkCustomId} ${talkPresenters}">${startTime} - ${endTime} # ${talkCustomId} ${talkPresenters}</li></ul>`;
                            } else {
                                talkTimeSummary += `<ul class="mt-2 text-wrap"><li title="${startTime} to ${endTime}">${startTime} - ${endTime} : ${talk.custom_abstract_desc}</li></ul>`;
                            }
                        });

                        let startDate = new Date(res.session_start_time);
                        let endDate = new Date(res.session_end_time);
                        let [hours, minutes, seconds] = res.session_end_time.split(' ')[1].split(':');
                        endDate.setHours(parseInt(hours), parseInt(minutes), parseInt(seconds));

                        events.push({
                            id: res.id,
                            title: `<strong>${res.session_title}</strong><br>${getTimeOfDate(res.session_start_time)} - ${getTimeOfDate(res.session_end_time)} <br> ${sessionChairs} <br>${talkTimeSummary}`,
                            description: res.description,
                            start: startDate.toISOString(),
                            end: endDate.toISOString(),
                            resourceId: res.rooms.room_id,
                            extendedProps: {
                                room_id: res.rooms.room_id,
                                room_name: res.rooms.name,
                            }
                        });
                    }
                });

                resolve(events);
            }).fail(reject);
        });
    }

    function convertToISOTimeOnly(timeString) {
        const [hours, minutes, seconds] = timeString.split(':');
        const date = new Date();
        date.setHours(parseInt(hours), parseInt(minutes), parseInt(seconds));
        return date.toISOString().split('T')[1].split('Z')[0];
    }

    function convertToISODateOnly(dateString) {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            throw new Error("Invalid date string");
        }
        return date.toISOString().split('T')[0];
    }

    function formatToISODateTime(dateString, timeString) {
        const date = new Date(dateString);
        const [hours, minutes, seconds] = timeString.split(':');
        date.setHours(parseInt(hours), parseInt(minutes), parseInt(seconds));
        return date.toISOString().split('.')[0];
    }

    function editEvent(info, allowed) {
        let schedulerModal = $('#schedulerModal');
        schedulerModal.modal('show');
        schedulerModal.find('.modal-body').html('');
        schedulerModal.find('.modal-title').html('');

        showSchedulerModal(info, allowed).then(function() {
            schedulerModal.find('button[type="submit"]').text('Update');
            schedulerModal.find('#updateID').val(info.id);
            $.get(baseUrlAdmin + `scheduler/get_one_json/${info.id}`, function(data) {
                let sessionChairs = [];
                if (data.scheduled_event.session_chair_ids) {
                    sessionChairs = JSON.parse(data.scheduled_event.session_chair_ids);
                }

                $('#floatingDay').val(data.scheduled_event.session_day ?? '');
                $('#floatingSessionTitle').val(data.scheduled_event.session_title ?? '');
                $('#floatingSessionDescription').val(data.scheduled_event.description ?? '');
                $('#floatingSessionType').val(data.scheduled_event.paper_type ?? '');
                $('#floatingDurationTalk').val(data.scheduled_event.talk_duration ?? '');
                $('#floatingDurationBreak').val(data.scheduled_event.break_duration ?? '');
                $('#floatingSessionNumber').val(data.scheduled_event.session_number ?? '');
                $("#floatingRooms").val(data.scheduled_event.room_id ?? '').change();
                $("#floatingSessionTracks").val(data.scheduled_event.session_track ?? '').change();

                sessionChairs.forEach((chair, index) => {
                    const elementId = `#floatingSessionChair${index + 1}`;
                    const $element = $(elementId);

                    if ($element.length) {
                        $element.val(chair ?? '').change();
                    }
                });

            });
        });
        schedulerModal.find('.modal-title').html(`Manage Session #: ${info.id}`);
    }

    function talksEvent(info) {
        let schedulerModal = $('#schedulerModal');
        schedulerModal.find('.modal-footer .btn.btn-primary').hide();
        const removedAbstractIds = [];
        const talk_details = [];
        let talkDetail = {};
        const sessionStart24 = get24hrTime(new Date(info.startStr));
        $.get(`${baseUrlAdmin}scheduler/render_talks/${info.id}`, function(response) {
            if (!response) return;

            schedulerModal.modal('show');
            schedulerModal.find('.modal-title').html(`<p>Assigning Talks to: #${info.id}</p>`);
            schedulerModal.find('.modal-body').html(response);
            schedulerModal.find('.modal-footer .btn-primary').attr('id', 'save-session-talks');

            let tableAbstract = schedulerModal.find('#abstractTable');
            let tableAddedAbstract = schedulerModal.find('#tableAddedAbstract');

            let sessionDuration = schedulerModal.find('.session-duration').data('value');
            let sessionDate = schedulerModal.find('.session-date').data('value');
            let talkDuration = schedulerModal.find('.session-talk-duration').data('value');
            let breakDuration = schedulerModal.find('.session-break-duration').data('value');

            let tableAddedAbstractArray = [];
            let durationInMinutes = talkDuration;
            let removedAddedTalksIds = [];

            // Add Abstract Button Click Handler
            schedulerModal.find('#addAbstractBtn').off('click').on('click', async function(e) {
                e.preventDefault();
                const button = $(this);
                button.prop('disabled', true);
                const abstract_ids = [];

                try {
                    tableAbstract.find(".row-select:checked").each(function () {
                        abstract_ids.push($(this).data('abstract-id'));
                    });

                    await new Promise((resolve, reject) => {
                        getAbstract(abstract_ids, function (data) {
                            try {
                                if (abstract_ids.length > 0) {
                                    $.each(abstract_ids, function (i, abstract_id) {
                                        const index = removedAddedTalksIds.indexOf(abstract_id);
                                        if (index !== -1) {
                                            removedAddedTalksIds.splice(index, 1);
                                        }
                                    });
                                }

                                if (!data) {
                                    resolve();
                                    return;
                                }

                                let startTime = new Date(info.startStr);
                                let startDate = new Date(info.startStr);
                                startDate = startDate.getDate();

                                const processAbstracts = async () => {
                                    for (let i = 0; i < data.length; i++) {
                                        const res = data[i];
                                        let presenters = getPresenters(res.authors);
                                        let endTime = addDuration(startTime, durationInMinutes);

                                        if (new Date(info.endStr) <= endTime) {
                                            toastr.error("Time already exceeded!");
                                            break;
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

                                        if (res.paper.submission_type == 'panel') {
                                            await createPanelTalkRows(res.paper, talkDetail, presenters, formattedStartTime, formattedEndTime, sessionStart24);
                                            updateTalkDuration(getTimeOfDate(new Date(info.startStr)));
                                        } else {
                                            tableAddedAbstract.find('tbody').append(createTalkRow(res.paper, talkDetail, presenters, formattedStartTime, formattedEndTime, sessionStart24));
                                            updateTalkDuration(getTimeOfDate(new Date(info.startStr)));
                                        }

                                        tableAddedAbstractArray.push({
                                            'id': res.paper.id,
                                            'paper': res.paper,
                                            'talks': talkDetail,
                                            'presenters': presenters,
                                            'formattedStartTime': formattedStartTime,
                                            'formattedEndTime': formattedEndTime
                                        });

                                        tableAbstract.find(`[data-abstract-id="${res.paper.id}"]`).closest('tr').hide().find('input[type="checkbox"]').prop('checked', false);

                                        removedAbstractIds.shift(res.paper.id);
                                        startTime = addDuration(endTime, breakDuration);
                                    }
                                };

                                processAbstracts().then(resolve).catch(reject);

                            } catch (error) {
                                reject(error);
                            }
                        });
                    });

                    // Duration Change Handler
                    tableAddedAbstract.off('change input', '.talk-duration').on('change input', '.talk-duration', function () {
                        updateTalkDuration(getTimeOfDate(new Date(info.startStr)));
                    });

                    // Remove Abstract Handler
                    tableAddedAbstract.off('click', '.remove').on('click', '.remove', function (e) {
                        e.preventDefault();
                        let abstractAddedId = $(this).data('abstract-id');

                        tableAddedAbstractArray = tableAddedAbstractArray.filter(item => item['abstract_id'] !== abstractAddedId);

                        if (!removedAbstractIds.includes(abstractAddedId)) {
                            removedAbstractIds.push(abstractAddedId);
                        }

                        tableAbstract.find(`[data-abstract-id="${abstractAddedId}"]`).closest('tr').show();
                        tableAddedAbstract.find(`tr[id="${abstractAddedId}"]`).remove();
                        $(this).closest('tr').remove();

                        tableAddedAbstract.find('.talk-duration').change();
                        removedAddedTalksIds.push(abstractAddedId);

                        updateTalkDuration(getTimeOfDate(new Date(info.startStr)));
                    });

                    // Save Session Talks
                    schedulerModal.find('#save-session-talks').off('click').on('click', function () {
                        let talksTable = $('#tableAddedAbstract');
                        let added_talk_details = [];
                        talksTable.find('tr').each(function () {
                            let abstract_id = $(this).attr('id');
                            if (abstract_id) {
                                let start_time = $(this).find('.start-time').text() ?? '';
                                let end_time = $(this).find('.end-time').text() ?? '';
                                let talk_duration = $(this).find('.talk-duration').val() ?? '';
                                let custom_desc = $(this).find('#talk_custom_desc').val() ?? '';
                                let paper_sub_id = $(this).data('paper-sub-id');
                                added_talk_details.push({
                                    'duration': talk_duration,
                                    'start_time': start_time,
                                    'end_time': end_time,
                                    'abstract_id': abstract_id,
                                    'break_duration': $(".session-break-duration").data('value'),
                                    'scheduler_event_id': info.id,
                                    'custom_desc': custom_desc,
                                    'paper_sub_id': paper_sub_id,
                                });
                            }
                        });

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
                        } else {
                            saveTalks(added_talk_details, removedAddedTalksIds);
                        }
                    });

                } catch (error) {
                    console.error('Error fetching abstracts:', error);
                    toastr.error('Failed to fetch abstracts. Please try again.');
                } finally {
                    button.prop('disabled', false);
                }
            });

            schedulerModal.find('#addCustomEventBtn').off('click').on('click', function(e) {
                let tableAddedAbstract = $("#tableAddedAbstract");
                let customEventCount = tableAddedAbstract.find('.customAddedEvent').length;
                customEventCount++;

                tableAddedAbstract.find('tbody').append(
                    `<tr id="custom_${customEventCount}" class="customAddedEvent">
                        <td><span class="start-time"></span> - <span class="end-time"></span></td>
                        <td><input type="number" class="talk-duration" style="width:50px" value="${talkDuration}"></td>
                        <td class="text-nowrap "></td>
                        <td><input type="text" name="talk_custom_desc" id="talk_custom_desc"></td>
                        <td class="text-nowrap">
                            <a class="btn btn-sm moveUp" onclick="moveUp(this)" data-abstract-id="custom_${customEventCount}"  data-initial-time="${sessionStart24}"><i class="fas fa-arrow-up"></i></a>
                            <a class="btn btn-sm moveDown" onclick="moveDown(this)" data-abstract-id="custom_${customEventCount}"  data-initial-time="${sessionStart24}"><i class="fas fa-arrow-down"></i></a>
                            <a class="btn btn-sm remove" data-abstract-id="custom_${customEventCount}"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>`
                );
                updateTalkDuration(getTimeOfDate(new Date(info.startStr)));
            });

            getTalks(talk_details, talkDetail, info, sessionStart24, sessionDate);

            function saveTalks(added_talk_details, removedAddedTalksIds) {
                if (!Array.isArray(added_talk_details)) {
                    console.error("Invalid data format for talks.");
                    toastr.error("Invalid data provided. Please try again.");
                    return;
                }

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
                let breakDurationVal = schedulerModal.find('.session-break-duration').data('value');
                let currentStartTime24 = sessionStart24;
                let duration = 0;
                let endTime24 = 0;

                rows.each(function (index) {
                    let row = $(this);
                    let durationInput = row.find('.talk-duration');
                    let endTimeElement = row.find('.end-time');

                    duration = parseFloat(durationInput.val());
                    endTime24 = addMinutesToTime(currentStartTime24, duration);

                    row.find('.start-time').text(getTimeOfDateFrom24hr(currentStartTime24));
                    row.find('.end-time').text(getTimeOfDateFrom24hr(endTime24));
                    endTimeElement.text(getTimeOfDateFrom24hr(endTime24));

                    currentStartTime24 = addMinutesToTime(endTime24, breakDurationVal);
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

        function createTalkRow(paper, talkDetail, presenters, startTime, endTime, sessionStart24) {
            let row = $(`<tr id="${paper.id}">`);
            row.append(`<td><span class="start-time"></span> - <span class="end-time"></span></td>`);
            row.append(`<td><input type="number" class="talk-duration" style="width:50px" data-abstract-id="${paper.id}" value="${talkDetail.duration}"></td>`);
            row.append(`<td class="text-nowrap ">${presenters}</td>`);
            row.append(`<td>Abstract ID: (<span class="fw-bold">${paper.custom_id})</span><br> ${stripTags(paper.title)}</td>`);
            row.append(`
                <td class="text-nowrap">
                    <a class="btn btn-sm moveUp" onclick="moveUp(this)" data-abstract-id="${paper.id}" data-initial-time="${sessionStart24}"><i class="fas fa-arrow-up"></i></a>
                    <a class="btn btn-sm moveDown" onclick="moveDown(this)" data-abstract-id="${paper.id}" data-initial-time="${sessionStart24}"><i class="fas fa-arrow-down"></i></a>
                    <a class="btn btn-sm remove" data-abstract-id="${paper.id}"><i class="fas fa-trash"></i></a>
                </td>
            `);
            return row;
        }

        async function createPanelTalkRows(paper, talkDetail, presenters, startTime, endTime, sessionStart24) {
            return new Promise((resolve, reject) => {
                const rows = [];
                getPanelsAbstract([paper.id]).then(function(panels) {
                    if (panels) {
                        $.each(panels.data[0].panelist_abstract, function(i, panel) {
                            let row = $(`<tr id="${panel.paper_id}" data-paper-sub-id="${panel.individual_panel_id}">`);
                            row.append(`<td><span class="start-time"></span> - <span class="end-time"></span></td>`);
                            row.append(`<td><input type="number" class="talk-duration" style="width:50px" data-abstract-id="${paper.id}" value="${talkDetail.duration}"></td>`);
                            row.append(`<td class="text-nowrap">${panel.name + ' '+ panel.surname}</td>`);
                            row.append(`<td>Abstract ID: (<span class="fw-bold">${panel.custom_id})</span><br> ${stripTags(panel.individual_panel_title)}</td>`);
                            row.append(`
                                <td class="text-nowrap">
                                    <a class="btn btn-sm moveUp" onclick="moveUp(this)" data-abstract-id="${paper.id}" data-initial-time="${sessionStart24}"><i class="fas fa-arrow-up"></i></a>
                                    <a class="btn btn-sm moveDown" onclick="moveDown(this)" data-abstract-id="${paper.id}" data-initial-time="${sessionStart24}"><i class="fas fa-arrow-down"></i></a>
                                    <a class="btn btn-sm remove" data-abstract-id="${paper.id}"><i class="fas fa-trash"></i></a>
                                </td>`);

                            rows.push(row);
                        });

                        rows.forEach(function(row) {
                            $('#tableAddedAbstract').find('tbody').append(row);
                        });

                        resolve();
                    } else {
                        reject('No panels found');
                    }
                }).catch(reject);
            });
        }

        function getTotalDuration(table, breakDuration) {
            let totalDuration = 0;
            let numTalks = table.find('.talk-duration').length;
            table.find('.talk-duration').each(function() {
                totalDuration += parseInt($(this).val(), 10) || 0;
            });
            totalDuration += (numTalks - 1) * breakDuration; // Breaks between talks only
            return totalDuration;
        }
    }

    async function getPanelsAbstract(abstract_panel_ids) {
        return $.post(`${baseUrlAdmin}/getAllPanelsWithId`, {
            abstract_panel_ids: abstract_panel_ids,
            submission_type: 'panel',
        }).done(function(response) {
            if (response.status == 'success') {
                return response.data;
            }
        });
    }

    function deleteEvent(info, allowed) {
        $.ajax({
            url: baseUrlAdmin + `talks/scheduled/${info.id}`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log(data)
                if (data.data && data.data.length > 0) {
                    Swal.fire({
                        title: "Cannot delete!",
                        text: "This session has talks assigned. Please remove all talks before deleting the session.",
                        icon: "error"
                    });
                    return false;
                } else {
                    confirmDelete(info);
                }
            },
            error: function() {
                toastr.error("Failed to fetch session details!");
            }
        });
    }

    function confirmDelete(info) {
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
                    if (data && data.status === 'success') {
                        Swal.fire({
                            title: "Deleted!",
                            text: "Your file has been deleted.",
                            icon: "success"
                        });
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

    function getTalks(talk_details, talkDetail, info, sessionStart24, sessionDate) {
        $.get(`${baseUrlAdmin}/talks/scheduled/${info.id}`, function (response) {
            $("#tableAddedAbstract").find('tbody').html('');
            if (response.status == 'success') {
                $.each(response.data, function (i, data) {
                    if (data.scheduler_event_id && data.scheduler_event_id == info.id) {
                        let presentersList = '';
                        let display_id = '';
                        let customAbstractTitle = '';

                        if (data.submission_type === 'panel' && data.scheduler_event_id == info.id) {
                            if (data.panelist) {
                                presentersList = `${data.panelist.name} ${data.panelist.surname}<br>`;
                                display_id = data.paper_sub.custom_id || '';
                                customAbstractTitle = data.paper_sub.individual_panel_title || '';
                            }
                        } else {
                            if (data.presenters.length > 0) {
                                $.each(data.presenters, function (j, presenter) {
                                    presentersList += `${presenter.user_name} ${presenter.user_surname}<br>`;
                                });
                            }
                            display_id = data.abstract_custom_id || '';
                            customAbstractTitle = data.abstract_title ? stripTags(data.abstract_title) : data.custom_abstract_desc || '';
                        }

                        const startTime = getTimeOfDate(data.time_start);
                        const endTime = getTimeOfDate(data.time_end);

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
                                    <a class="btn btn-sm moveUp" onclick="moveUp(this)" data-abstract-id="${data.abstract_id}" data-initial-time="${sessionStart24}"><i class="fas fa-arrow-up"></i></a>
                                    <a class="btn btn-sm moveDown" onclick="moveDown(this)" data-abstract-id="${data.abstract_id}" data-initial-time="${sessionStart24}"><i class="fas fa-arrow-down"></i></a>
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
        let month = ('0' + (date.getMonth() + 1)).slice(-2);
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

    function get24hrTime(date) {
        let hours = date.getHours().toString().padStart(2, '0');
        let minutes = date.getMinutes().toString().padStart(2, '0');
        return `${hours}:${minutes}`;
    }

    function get24hrTimeFrom12hr(time12) {
        // Simple conversion assuming time12 is like "10:00 AM"
        let [timePart, ampm] = time12.split(' ');
        let [hours, minutes] = timePart.split(':');
        hours = parseInt(hours);
        if (ampm === 'PM' && hours !== 12) {
            hours += 12;
        } else if (ampm === 'AM' && hours === 12) {
            hours = 0;
        }
        return `${hours.toString().padStart(2, '0')}:${minutes}`;
    }

    function getTimeOfDateFrom24hr(time24) {
        let [hours, minutes] = time24.split(':').map(Number);
        let date = new Date();
        date.setHours(hours, minutes, 0, 0);
        return date.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit', hour12: true});
    }

    function getHourMin(time){
        return time.split(':').slice(0, 2).join(':');
    }

    function moveDown(element) {
        const row = $(element).closest('tr');
        const nextRow = row.next();

        const initialTime = element.dataset.initialTime;
        if (nextRow.length) {
            // Store original times
            let startA = row.find('.start-time').text();
            let endA = row.find('.end-time').text();
            let startB = nextRow.find('.start-time').text();
            let endB = nextRow.find('.end-time').text();

            // Swap DOM positions
            row.insertAfter(nextRow);

            // Now swap times to switch with positions
            // After swap, nextRow is now before row, so upper (nextRow) gets original A times, lower (row) gets original B
            nextRow.find('.start-time').text(startA);
            nextRow.find('.end-time').text(endA);
            row.find('.start-time').text(startB);
            row.find('.end-time').text(endB);

            // No need for full recalc since adjacent swap and times switched
        } else {
            toastr.info('This is already the last row.');
        }
    }

    function moveUp(element) {
        const row = $(element).closest('tr');
        const prevRow = row.prev();

        const initialTime = element.dataset.initialTime;
        if (prevRow.length) {
            // Store original times
            let startA = row.find('.start-time').text();
            let endA = row.find('.end-time').text();
            let startB = prevRow.find('.start-time').text();
            let endB = prevRow.find('.end-time').text();

            // Swap DOM positions
            row.insertBefore(prevRow);

            // Now swap times to switch with positions
            // After swap, row is now before prevRow, so upper (row) gets original B times, lower (prevRow) gets original A
            row.find('.start-time').text(startB);
            row.find('.end-time').text(endB);
            prevRow.find('.start-time').text(startA);
            prevRow.find('.end-time').text(endA);

            // No need for full recalc since adjacent swap and times switched
        } else {
            toastr.info('This is already the first row.');
        }
    }

    function updateTalkDurations(initialTime) {
        const table = $('#tableAddedAbstract tbody');
        const rows = table.find('tr');
        let currentTime = initialTime; // Already 24hr
        const breakDuration = parseFloat($('.session-break-duration').data('value')) || 0;
        rows.each(function (index) {
            const row = $(this);
            const duration = parseFloat(row.find('.talk-duration').val()) || 0;
            let endTime = addMinutesToTime(currentTime, duration);
            row.find('.start-time').text(getTimeOfDateFrom24hr(currentTime));
            row.find('.end-time').text(getTimeOfDateFrom24hr(endTime));
            currentTime = addMinutesToTime(endTime, breakDuration);
        });
    }

    function addMinutesToTime(startTime, minutesToAdd) {
        const [hours, minutes] = startTime.split(':').map(Number);
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

    function getIcon(value) {
        if (value && (value === '1' || value.toLowerCase() === 'yes')) return '✅';
        if (value && (value === '2' || value.toLowerCase() === 'no')) return '❌';
        return '⬜';
    }

</script>

<script>
    // Function to open the side nav
    function toggleNav() {
        const sideNav = document.getElementById("sidenav");
        const mainContent = document.getElementById("calendar");
        const abstract_list = document.getElementById("abstract_list");

        sideNav.classList.toggle("open");
        mainContent.classList.toggle("open");
        abstract_list.classList.toggle("open");
    }
</script>