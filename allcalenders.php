<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header('Location: google-login.php');
    exit();
}

if (isset($_SESSION['access_token'])) {
    echo $_SESSION['access_token'];
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

    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

</head>

<body>
    <div class="container mt-4">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
            Launch demo modal
        </button>



        <table id="myTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sumary</th>
                    <th>Access role</th>
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
                    <form id="createCalender" class="row g-3">
                        <div class="col-md-6">
                            <label for="summary" class="form-label">Summary</label>
                            <input type="text" class="form-control" id="summary" required>
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="form-label">Description</label>
                            <input type="text" class="form-control" id="description">
                        </div>

                        <div class="col-md-12 mt-2">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {

            // List all the calenders
            // console.log('<?= $_SESSION['access_token'] ?>');
            // Event details
            const params = new URLSearchParams({
                fields: 'items(id,summary,timeZone,accessRole)',
                minAccessRole: 'owner'
            });
            const parameters = params.toString();
            $.ajax({
                type: 'GET',
                url: `https://www.googleapis.com/calendar/v3/users/me/calendarList?${parameters}`,
                headers: {
                    'Authorization': 'Bearer <?= $_SESSION['access_token'] ?>',
                },
                success: function(response) {
                    // $('#response').html(response.items);
                    // console.log(response.items);
                    var i = 1;
                    response.items.forEach(element => {
                        var ele = `<tr>
                                        <td>${i}</td>
                                        <td><a href="allevents.php?id=${element['id']}">${element['summary']}</a></td>
                                        <td>${element['accessRole']}</td>
                                    </tr>`;
                        $('#response').append(ele);
                        // console.log(element['summary']);
                        i++;
                    });
                },
                error: function(response) {
                    alert(response.responseJSON.message);
                }
            });


            // Create a new calenders
            $('#createCalender').submit(function(e) {
                e.preventDefault();

                var summary = $('#summary').val();
                var description = $('#description').val();

                console.log(summary);
                console.log(description);

                $.ajax({
                    type: "POST",
                    url: 'https://www.googleapis.com/calendar/v3/calendars',
                    headers: {
                        'Authorization': 'Bearer <?= $_SESSION['access_token'] ?>',
                    },
                    data: {
                        "summary": summary,
                        "description": description
                    },
                    contentType: 'application/json; charset=utf-8',
                    success: function(response) {
                        window.location.reload();
                    },
                    error: function(response) {
                        console.log(response.responseJSON);
                    }
                });

            });


        });
    </script>

</body>

</html>