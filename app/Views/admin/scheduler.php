<!-- v2 -->
<link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />

<link rel="stylesheet" href="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.css" />

<link rel="stylesheet" href="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.css" />


<style>
    .lh-unset{
        line-height:unset;
    }
    .text-green{
        color:#2aa69c
    }
</style>

</script>
<?php echo view('admin/common/menu'); ?>
<?php //print_r($learning_objectives); exit;?>
<main class="" style="padding-bottom:500px">
    <div class="container prevent-select">
       <nav class="navbar navbar-light bg-light">
            <form class="form-inline">
                <button class="btn btn-outline-success tui-monthly" type="button">Monthly</button>
                <button class="btn btn-outline-info tui-daily" type="button">Day</button>
                <button class="btn btn-outline-info tui-weekly" type="button">Weekly</button>
                <button class="btn btn-outline-success tui-2weeks" type="button">2Weeks</button>
                <button class="btn btn-outline-info tui-3weeks" type="button">3Weeks</button>
                <!-- <button class="btn btn-outline-success tui-narrow-weekend" type="button">Narrow Weekends</button>
                <button class="btn btn-outline-info tui-hide-weekend" type="button">Hide Weekends</button>
                <button class="btn btn-outline-success tui-duplicates" type="button">Duplicates</button>
                <button class="btn btn-outline-info tui-task-only" type="button">Task only</button> -->
                <!-- <button class="btn btn-outline-success" type="button">Timezone</button> -->
            </form>
        </nav>
        <div class="border p-3 border-5 border-info rounded  shadow mt-3">
            <div class="fw-bold"> PRiSM Scheduler</div>
            <select id="monthDropdown" class=" btn btn-outline-secondary">
            <option class=" btn btn-outline-secondary" value="0">January</option>
            <option value="1">February</option>
            <option value="2">March</option>    
            <option value="3">April</option>
            <option value="4">May</option>
            <option value="5">June</option>
            <option value="6">July</option>
            <option value="7">August</option>
            <option value="8">September</option>
            <option value="9">October</option>
            <option value="10">November</option>
            <option value="11">December</option>
        </select>

        <select id="yearDropdown" class="btn btn-outline-secondary">
            <?php 
            $futureDate = date('Y-m-d', strtotime('+5 year', strtotime(date('Y'))));    
            for($i=1990; $i <= $futureDate; $i++) :?>
            <option class="btn btn-outline-secondary" <?= (date('Y') == $i)?'selected':''?>> <?=$i?> </option>
            <?php endfor; ?>
        </select>

        <button class="btn btn-success tui-today" type="button">Today</button>
        <button class="btn btn-success rounded-circle tui-prev" type="button"><i class="fas fa-less-than"></i></button>
        <button class="btn btn-success rounded-circle tui-next" type="button"><i class="fas fa-greater-than"></i></button>

        <div id="calendar" style="height: 600px;"></div>
        </div>
        <div id="custom-textbox">
        <input type="text" id="additional-textbox">
        </div>


    </div>
</main>


<!-- Modal -->
<div class="modal fade" id="customPopUp" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color:#2aa69c">
        <h5 class="modal-title" id="exampleModalLabel">Schedule</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0 fw-bolder text-green" id="basic-addon1">Day </span>
            </div>
            <input type="text" name="session-day" class="form-control" placeholder="Session Day" aria-label="day" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0" id="basic-addon1"><i class="fas fa-clock lh-unset"></i> </span>
            </div>
            <input type="text" name="time-from" class="form-control" placeholder="From" aria-label="From" aria-describedby="basic-addon1">
            
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0" id="basic-addon1"><i class="fas fa-clock lh-unset"></i></span>
            </div>
            <input type="text" name="session-day" class="form-control" placeholder="To" aria-label="To" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0 fw-bolder text-green" id="basic-addon1">Session Title</span>
            </div>
            <input type="text" name="session-title" class="form-control" placeholder="Session Title..." aria-label="day" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0 fw-bolder text-green" id="basic-addon1">Session Description</span>
            </div>
            <input type="text" name="session-description" class="form-control" placeholder="Session Description..." aria-label="day" aria-describedby="basic-addon1">
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0 fw-bolder text-green" id="basic-addon1">Session Type</span>
            </div>
            <input type="text" name="session-type" class="form-control" placeholder="Session Type..." aria-label="day" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0 fw-bolder text-green" id="basic-addon1">Talk Duration</span>
            </div>
            <input type="text" name="talk-duration" class="form-control" placeholder="Talk Duration..." aria-label="day" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0 fw-bolder text-green" id="basic-addon1">Break Duration</span>
            </div>
            <input type="text" name="break-duration" class="form-control" placeholder="Break Duration" aria-label="day" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0 fw-bolder text-green" id="basic-addon1">Session Number</span>
            </div>
            <input type="text" name="session-number" class="form-control" placeholder="Number" aria-label="day" aria-describedby="basic-addon1">
        </div>
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text rounded-0 fw-bolder text-green" id="basic-addon1">Event Rooms</span>
            </div>
            <input type="text" name="event-rooms" class="form-control" placeholder="Room" aria-label="day" aria-describedby="basic-addon1">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveSchedule">Save changes</button>
      </div>
    </div>
  </div>
</div>


<script src="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.js"></script>
<script src="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.js"></script>
<script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>

<script src="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.js"></script>
<script>
    let baseUrlAdmin = "<?=base_url().'/admin/'?>";
    let baseUrlScheduler = "<?=base_url().'/admin/scheduler/'?>";
    const Calendar = tui.Calendar;
    $(function(){
        
    var events = [];


    const $container  = $('#calendar');
    const TimePicker = $('#tui-time-picker');
    const options = {
        defaultView: 'month',
        
        timezone: {
            zones: [
            {
                timezoneName: 'Asia/Seoul',
                displayLabel: 'Seoul',
            },
            ],
        },
        calendars: [
            {
            id: 'cal1',
            name: 'Personal',
            backgroundColor: '#03bd9e',
            },
            {
            id: 'cal2',
            name: 'Work',
            backgroundColor: '#00a9ff',
            },
        ],
        button:{
            className: 'btn btn-primary'
          },
        template: {
            milestone: function(schedule) {
                return '<span style="color:red;"><i class="fa fa-flag"></i> ' + schedule.title + '</span>';
            },
            milestoneTitle: function() {
                return 'Milestone';
            },
            task: function(schedule) {
                return '&nbsp;&nbsp;#' + schedule.title;
            },
            task2: function(schedule) {
                return '&nbsp;&nbsp;#' + schedule.title;
            },
            taskTitle: function() {
                return '<label><input type="checkbox" />Task</label>';
            },
            allday: function(schedule) {
                return schedule.title + ' <i class="fa fa-refresh"></i>';
            },
            alldayTitle: function() {
                return 'All Day';
            },
            time: function(schedule) {
                return schedule.title + ' <i class="fa fa-refresh"></i>' + schedule.start;
            },
            monthMoreTitleDate: function(date) {
                date = new Date(date);
                return tui.util.formatDate('MM-DD', date) + '(' + daynames[date.getDay()] + ')';
            },
            monthMoreClose: function() {
                return '<i class="fa fa-close"></i>';
            },
            // monthGridHeader: function(model) {
            //     var date = parseInt(model.date.split('-')[2], 10);
            //     var classNames = [];

            //     classNames.push(config.classname('weekday-grid-date'));
            //     if (model.isToday) {
            //         classNames.push(config.classname('weekday-grid-date-decorator'));
            //     }

            //     return '<span class="' + classNames.join(' ') + '">' + date + '</span>';
            // },
            monthGridHeaderExceed: function(hiddenSchedules) {
                return '<span class="calendar-more-schedules">+' + hiddenSchedules + '</span>';
            },

            monthGridFooter: function() {
                return '<div class="calendar-new-schedule-button">New Schedule</div>';
            },

            monthGridFooterExceed: function(hiddenSchedules) {
                return '<span class="calendar-footer-more-schedules">+ See ' + hiddenSchedules + ' more events</span>';
            },
            weekDayname: function(dayname) {
                return '<span class="calendar-week-dayname-name">' + dayname.dayName + '</span><br><span class="calendar-week-dayname-date">' + dayname.date + '</span>';
            },
            monthDayname: function(dayname) {
                return '<span class="calendar-week-dayname-name">' + dayname.label + '</span>';
            },
            timegridDisplayPrimaryTime: function(time) {
                var meridiem = time.hour < 12 ? 'am' : 'pm';

                return time.hour + ' ' + meridiem;
            },
            timegridDisplayTime: function(time) {
                return time.hour + ':' + time.minutes;
            },
            goingDuration: function(model) {
                var goingDuration = model.goingDuration;
                var hour = parseInt(goingDuration / SIXTY_MINUTES, 10);
                var minutes = goingDuration % SIXTY_MINUTES;

                return 'GoingTime ' + hour + ':' + minutes;
            },
            comingDuration: function(model) {
                var goingDuration = model.goingDuration;
                var hour = parseInt(goingDuration / SIXTY_MINUTES, 10);
                var minutes = goingDuration % SIXTY_MINUTES;

                return 'ComingTime ' + hour + ':' + minutes;
            },
            popupDetailRepeat: function(model) {
                return model.recurrenceRule;
            },
            popupDetailBody: function(model) {
                return model.body;
            }
        },
        useCreationPopup: true,
        useDetailPopup: true,
          
    };

    const calendar = new tui.Calendar($container[0], options);
    // const event = calendar.getEvent('click', calendarId);
    // console.log(event)
   
    calendar.setOptions({
        useFormPopup: false,
        useDetailPopup: true,
        });

    createEvent(calendar);


    $('#monthDropdown, #yearDropdown').on('change', function(){
        const selectedMonth = parseInt($('#monthDropdown').val()); // Get the selected month from the dropdown
        const selectedYear =  parseInt($('#yearDropdown').val()); //

        calendar.setDate(new Date(selectedYear, selectedMonth, 1));

    })


    // Creating an event through popup
    calendar.on('beforeCreateEvent', (eventObj) => {
        console.log(eventObj);
        calendar.createEvents([
            {
            ...eventObj
            },
        ]);
    });

// Append the custom textbox to the calendar popup container
// $('.toastui-calendar-form-container').append($('#custom-textbox'))
    
 let increment = 1;
    $('.tui-today').on('click', function(){
        increment = 1;
        calendar.today();
        const date = new Date()
        // console.log('year'+ date.getFullYear())
        $('#monthDropdown').val(new Date().getMonth());
        $('#yearDropdown').val(new Date().getFullYear());
        // console.log(calendar.getViewName())
    })

   
    $('.tui-next').on('click', function(){
       
        calendar.next();
        $('#monthDropdown').val(calendar.getDate().getMonth());
        $('#yearDropdown').val(calendar.getDate().getFullYear());
    })

    $('.tui-prev').on('click', function(){
        calendar.prev();
        $('#monthDropdown').val(calendar.getDate().getMonth());
        $('#yearDropdown').val(calendar.getDate().getFullYear());
    })


    $('.tui-monthly').on('click', function(){
        calendar.changeView('month', true);
    })

    $('.tui-daily').on('click', function(){
        calendar.changeView('day', true);
    })

     $('.tui-weekly').on('click', function(){
        calendar.changeView('week', true);
    })

    $('.tui-2weeks').on('click', function(){
        calendar.setOptions({month: {visibleWeeksCount: 2}}, true); // or null
        calendar.changeView('month', true);
    })

    $('.tui-3weeks').on('click', function(){
        calendar.setOptions({month: {visibleWeeksCount: 3}}, true); // or null
        calendar.changeView('month', true);
    })

    $('.tui-narrow-weekend').on('click', function(){
         calendar.setOptions({week: {workweek: true}}, true); // or null
        // calendar.changeView('month', true);
    })

    $('.tui-hide-weekend').on('click', function(){
        calendar.changeView('workweek', true);
    })

    $('.tui-duplicates').on('click', function(){
        calendar.changeView('collapseDuplicateEvents', true);
    })

    $('.tui-task-only').on('click', function(){
        calendar.changeView('taskView', true);
    })

    
})




// function openCreateEventPopup(callback) {
//     let popUpField ='<div class="toastui-calendar-popup-section">' +
//         '<div class="toastui-calendar-popup-section-item toastui-calendar-popup-section-location">' +
//         '<span class="toastui-calendar-icon toastui-calendar-ic-location"></span>' +
//         '<input name="location" class="toastui-calendar-content" placeholder="Location">' +
//         '</div>' +
//         '</div>';
//
//     $('.toastui-calendar-form-container').append(popUpField)
//     // $('#customPopUp').h(popUpField)
//     $('#saveSchedule').on('click', function(){
//         var eventTitle = 'test';
//         var eventDate = new Date();
//
//          if (eventTitle && eventDate) {
//             // Prepare new event data
//             var newEvent = {
//                 id: 123,
//                 title: eventTitle,
//                 start: eventDate + 'T00:00:00',
//                 end: eventDate + 'T23:59:59',
//                 raw: {
//                     customPropIneedToDisplay: ' '
//                 }
//             };
//
//             // Call the callback function with the new event data
//             callback(newEvent);
//              $('#customPopUp').modal('hide')
//         } else {
//             alert('Please enter event title and date.');
//         }
//     })
// }

function addSchedule(){
    $.post(baseUrlScheduler + 'add', {
        
    }, function (response) {
        console.log(response)
    });
}


function getAbstractAcceptance() {
    return new Promise((resolve, reject) => {
        $.post(baseUrlAdmin + 'scheduler/getAllPapers',
            {
              "submission_type": "paper"
            },
            function (response) {
            response = JSON.parse(response);
            const events = [];
            $.each(response, function (index, res) {
                if(res.presentation_date !== null){
                events.push({
                    id: res.id,
                    calendarId: 'cal1',
                    title: res.title,
                    start: res.presentation_date,
                    end: res.presentation_date,
                    isAllday: true,
                    category: 'allday',
                });
            }
            });

            if (events.length > 0) {
                // Resolve the promise with the events data
                resolve(events);
            } else {
                // Reject the promise with an error message
                reject(new Error('No abstracts found.'));
            }
        });
    });
}

async function createEvent(calendar) {
    try {
   
        calendar.clear();
        const dbEvents = await getAbstractAcceptance();
        console.log(dbEvents);
        calendar.createEvents(dbEvents)
        // calendar.render();
    } catch (error) {
        console.error(error.message);
    }
}


</script>