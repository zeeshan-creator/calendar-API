<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: google-login.php');
    exit();
}

if (isset($_SESSION['access_token'])) {
    // echo $_SESSION['access_token'];
}

if (isset($_GET['id'])) {
    $calender_id = $_GET['id'];
} else {
    header('Location: allcalenders.php');
    exit();
}




?>
<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.1.9/jquery.datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.3/moment.min.js"></script>

    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

</head>
<style type="text/css">
    #form-container {
        width: 400px;
        margin: 100px auto;
    }

    input[type="text"] {
        border: 1px solid rgba(0, 0, 0, 0.15);
        font-family: inherit;
        font-size: inherit;
        padding: 8px;
        border-radius: 0px;
        outline: none;
        display: block;
        margin: 0 0 20px 0;
        width: 100%;
        box-sizing: border-box;
    }

    select {
        border: 1px solid rgba(0, 0, 0, 0.15);
        font-family: inherit;
        font-size: inherit;
        padding: 8px;
        border-radius: 2px;
        display: block;
        width: 100%;
        box-sizing: border-box;
        outline: none;
        background: none;
        margin: 0 0 20px 0;
    }

    .input-error {
        border: 1px solid red !important;
    }

    #event-date {
        display: none;
    }

    #create-update-event {
        background: none;
        width: 100%;
        display: block;
        margin: 0 auto;
        border: 2px solid #2980b9;
        padding: 8px;
        background: none;
        color: #2980b9;
        cursor: pointer;
    }

    #delete-event {
        background: none;
        width: 100%;
        display: block;
        margin: 20px auto 0 auto;
        border: 2px solid #2980b9;
        padding: 8px;
        background: none;
        color: #2980b9;
        cursor: pointer;
    }
</style>

<body>
    <div class="container mt-4">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
            Add Event
        </button>


        <table id="myTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sumary</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id='response'>

            </tbody>
        </table>




    </div>



    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="form-container">
                        <input type="text" id="event-title" placeholder="Event Title" autocomplete="off" />
                        <select id="event-type" autocomplete="off">
                            <option value="FIXED-TIME">Fixed Time Event</option>
                            <option value="ALL-DAY">All Day Event</option>
                        </select>
                        <p id="Repeat title"> Repeat: </p>
                        <select id="repeat-options" autocomplete="off">
                            <option value="EVERY-WEEK">Every week</option>
                            <option value="NEVER">Never</option>
                        </select>
                        <p id="Repeats-ending-title"> For how long would you like this event to repeat? </p>
                        <select id="repeat-ending-options" autocomplete="off">
                            <option value="END-DATE">Select an end date.</option>
                            <option value="ONGOING">Repeat forever</option>
                        </select>

                        <input type="text" id="repeat-end-time" placeholder="Repeat Event End Time" autocomplete="off" />
                        <input type="text" id="event-start-time" placeholder="Event Start Time" autocomplete="off" />
                        <input type="text" id="event-end-time" placeholder="Event End Time" autocomplete="off" />
                        <input type="text" id="event-date" placeholder="Event Date" autocomplete="off" />

                        <button id="create-update-event" data-operation="create" data-event-id="">Create Event</button>
                        <button id="delete-event" style="display:none">Delete Event</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {

            // console.log('<?= $_SESSION['access_token'] ?>');

            // Event details
            const params = new URLSearchParams({
                fields: 'items(id,summary,timeZone,accessRole)',
                minAccessRole: 'owner'
            });
            const parameters = params.toString();

            $.ajax({
                type: 'GET',
                url: `https://www.googleapis.com/calendar/v3/calendars/<?= $calender_id ?>/events`,
                headers: {
                    'Authorization': 'Bearer <?= $_SESSION['access_token'] ?>',
                },
                success: function(response) {
                    // $('#response').html(response.items);
                    // console.log(response.items);

                    var i = 1;
                    response.items.forEach(element => {

                        var start = moment(element['start'].dateTime).format('lll');
                        var end = moment(element['end'].dateTime).format('lll');

                        var ele = `<tr>
                                        <td>${i}</td>
                                        <td><a href="events.php?id=${element['id']}">${element['summary']}</a></td>
                                        <td>${start}</td>
                                        <td>${end }</td>
                                        <td><a href="delete.php?eventId=${element['id']}&calendarId=${param}">Delete</a></td>
                                    </tr>`;
                        $('#response').append(ele);

                        // console.log(response.items[0]);
                        i++;
                    });
                },
                error: function(response) {
                    alert(response.responseJSON.message);
                }
            });

        });
    </script>

    <script>
        let searchParams = new URLSearchParams(window.location.search);
        let param = searchParams.get('id')

        // Selected time should not be less than current time
        function AdjustMinTime(ct) {
            var dtob = new Date(),
                current_date = dtob.getDate(),
                current_month = dtob.getMonth() + 1,
                current_year = dtob.getFullYear();

            var full_date = current_year + '-' +
                (current_month < 10 ? '0' + current_month : current_month) + '-' +
                (current_date < 10 ? '0' + current_date : current_date);

            if (ct.dateFormat('Y-m-d') == full_date)
                this.setOptions({
                    minTime: 0
                });
            else
                this.setOptions({
                    minTime: false
                });
        }

        // DateTimePicker plugin : http://xdsoft.net/jqplugins/datetimepicker/
        // sending jquery to get date
        $("#event-start-time, #event-end-time").datetimepicker({
            format: 'Y-m-d H:i',
            minDate: 0,
            minTime: 0,
            step: 5,
            onShow: AdjustMinTime,
            onSelectDate: AdjustMinTime
        });
        $("#event-date, #repeat-end-time").datetimepicker({
            format: 'Y-m-d',
            timepicker: false,
            minDate: 0
        });

        // hiding or showing extra buttons if exact time is chosen
        $("#event-type").on('change', function(e) {
            if ($(this).val() == 'ALL-DAY') {
                $("#event-date").show();
                $("#event-start-time, #event-end-time").hide();
            } else {
                $("#event-date").hide();
                $("#event-start-time, #event-end-time").show();
            }
        });

        // hiding or showing extra buttons if repeat event is chosen
        $("#repeat-options").on('change', function(e) {
            if ($(this).val() == 'EVERY-WEEK') {
                $("#Repeats-ending-title").show(); // show title
                $("#repeat-ending-options").show(); // show options
                $("#repeat-end-time").show();
            } else {
                $("#Repeats-ending-title").hide(); // show title
                $("#repeat-ending-options").hide(); // show options
                $("#repeat-end-time").hide();
            }
        });

        // hiding or showing extra buttons if an end date is chosen for a repeat event
        $("#repeat-ending-options").on('change', function(e) {
            if ($(this).val() == 'END-DATE') {
                $("#repeat-end-time").show(); // show option to chose end date
            } else {
                $("#repeat-end-time").hide();
            }
        });

        // Send an ajax request to create event
        $("#create-update-event").on('click', function(e) {
            var blank_reg_exp = /^([\s]{0,}[^\s]{1,}[\s]{0,}){1,}$/, // creating a regular expression
                error = 0,
                parameters;

            $(".input-error").removeClass('input-error');

            if (!blank_reg_exp.test($("#event-title").val())) { // testing regular expression against string
                $("#event-title").addClass('input-error');
                error = 1;
            }

            if ($("#event-type").val() == 'FIXED-TIME') {
                if (!blank_reg_exp.test($("#event-start-time").val())) {
                    $("#event-start-time").addClass('input-error');
                    error = 1;
                }

                if (!blank_reg_exp.test($("#event-end-time").val())) {
                    $("#event-end-time").addClass('input-error');
                    error = 1;
                }
            } else if ($("#event-type").val() == 'ALL-DAY') {
                if (!blank_reg_exp.test($("#event-date").val())) {
                    $("#event-date").addClass('input-error');
                    error = 1;
                }
            }

            if ($("#repeat-options").val() == 'EVERY-WEEK') { // only option is to repeat weekly
                if (!blank_reg_exp.test($("#repeat-options").val())) {
                    $("#repeat-options").addClass('input-error');
                    error = 1;
                }
            } else { // else event never repeats

            } // HERE


            if (error == 1)
                return false;

            if ($("#event-type").val() == 'FIXED-TIME') {
                // If end time is earlier than start time, then interchange them
                if ($("#event-end-time").datetimepicker('getValue') < $("#event-start-time").datetimepicker('getValue')) {
                    var temp = $("#event-end-time").val();
                    $("#event-end-time").val($("#event-start-time").val());
                    $("#event-start-time").val(temp);
                }
            }


            // Event details - NEED TO ADD RECURRANCE HERE!
            parameters = {
                calendarId: param,
                title: $("#event-title").val(),
                event_time: {
                    start_time: $("#event-type").val() == 'FIXED-TIME' ? $("#event-start-time").val().replace(' ', 'T') + ':00' : null,
                    end_time: $("#event-type").val() == 'FIXED-TIME' ? $("#event-end-time").val().replace(' ', 'T') + ':00' : null,
                    event_date: $("#event-type").val() == 'ALL-DAY' ? $("#event-date").val() : null
                },
                all_day: $("#event-type").val() == 'ALL-DAY' ? 1 : 0,
                operation: $(this).attr('data-operation'),
                recurrence: $("#repeat-options").val() == 'EVERY-WEEK' ? 1 : 0, // returns 1 if event is set to repeat weekly
                // returns a date in 2100 if set to ongoing, else returns the end date set by the user
                recurrence_end: $("#repeat-ending-options").val() == 'ONGOING' ? "2100-01-01" : $("#repeat-end-time").val(),
                event_id: $(this).attr('data-operation') == 'create' ? null : $(this).attr('data-event-id')
            };

            $("#create-update-event").attr('disabled', 'disabled');
            $.ajax({
                type: 'POST',
                url: 'ajax.php',
                data: {
                    event_details: parameters
                },
                dataType: 'json',
                success: function(response) {
                    window.location.reload();
                    $("#create-update-event").removeAttr('disabled');

                    if (parameters.operation == 'create') {
                        $("#create-update-event").text('Update Event').attr('data-event-id', response.event_id).attr('data-operation', 'update');
                        $("#delete-event").show();
                        // alert('Event created with ID : ' + response.event_id);
                    } else if (parameters.operation == 'update') {
                        alert('Event ID ' + parameters.event_id + ' updated');
                    }
                },
                error: function(response) {
                    $("#create-update-event").removeAttr('disabled');
                    alert(response.responseJSON.message);
                }
            });
        });

        // Send an ajax request to delete event
        $("#delete-event").on('click', function(e) {
            // Event details
            var parameters = {
                operation: 'delete',
                event_id: $("#create-update-event").attr('data-event-id')
            };

            $("#create-update-event").attr('disabled', 'disabled');
            $("#delete-event").attr('disabled', 'disabled');
            $.ajax({
                type: 'POST',
                url: 'ajax.php',
                data: {
                    event_details: parameters
                },
                dataType: 'json',
                success: function(response) {
                    $("#delete-event").removeAttr('disabled').hide();

                    $("#form-container input").val('');
                    $("#create-update-event").removeAttr('disabled');
                    $("#create-update-event").text('Create Event').attr('data-event-id', '').attr('data-operation', 'create');

                    alert('Event ID ' + parameters.event_id + ' deleted');
                },
                error: function(response) {
                    $("#delete-event").removeAttr('disabled');
                    alert(response.responseJSON.message);
                }
            });
        });
    </script>


</body>

</html>