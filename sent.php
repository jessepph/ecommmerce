<?php include 'connection.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Email sent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/mail.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <style type="text/css">
        table {
          display: block;
          height: 450px;
          overflow-y: scroll;
          width: 850px !important;
        }
         #chat-table td {
            font-size: 12px;
            font-weight: normal;
         }
        #chat-table {
            margin-top: 81px;
            border-top: 0.5 px gray solid;
            border-bottom:1px gray solid;
        }
        * {
          scrollbar-width: thin;
          scrollbar-color: blue orange;
        }

        /* Works on Chrome, Edge, and Safari */
        *::-webkit-scrollbar {
          width: 8px;
        }

        *::-webkit-scrollbar-track {
          background: white;
        }

        *::-webkit-scrollbar-thumb {
          background-color: gray;
          border-radius: 20px;
          border: 3px solid gray;
        }
    </style>
</head>
<body>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container">
<div class="row">
    <!-- BEGIN INBOX -->
    <div class="col-md-12">
        <div class="grid email">
            <div class="grid-body">
                <div class="row">
                    <!-- BEGIN INBOX MENU -->
                    <?php include('sidebar.php') ?>
                    <!-- END INBOX MENU -->
                    
                    <!-- BEGIN INBOX CONTENT -->
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-sm-6">
                                <label style="margin-right: 8px;" class="">
                                    <div class="icheckbox_square-blue" style="position: relative;"><input type="checkbox" id="check-all" class="icheck" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"><ins class="iCheck-helper" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins></div>
                                </label>
                            </div>

  
                        </div>
                        
                        <div class="padding"></div>
                        
                        <?php if(isset($_GET['id']))
                        {
                            $mailId = $_GET['id'];
                        ?>

                        <div class="table-responsive">
                            <table class="table" id="chat-table">
                                <tbody>
                                    <tr>
                                        <td>
                                            <a href="<?php echo 'sent.php' ?>" style="font-size:20px"><i  class="fa fa-chevron-circle-left"></i></a> &nbsp;
                                            <a href="<?php echo 'action.php?sentMailId='. $mailId ?>" style="font-size:20px; color: red;"><i  class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="subject">

                                                <?php
                                                $sql = "SELECT * from mails where id = '$mailId' ";
                                                $result = mysqli_query($conn, $sql);
                                                $row = mysqli_fetch_array($result);
                                                echo '<b>' .$row['subject'] . '</b>
                                                &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp' .'
                                                <i class="fa fa-angle-left"></i> ' .$row['mail_to'] .' <i class="fa fa-angle-right"></i>';
                                                echo '<span style="margin-left:170px">' . date('M-d-Y H:i:A', strtotime($row['date_time'])) . '</span>';
                                                echo '</br></br>' .$row['body']. '</br>';
                                                ?>
                                        </td>
                                        <tr>
                                            <td>
                                                <i class="fa fa-arrow-circle-left"></i>
                                                <a href="">Reply</a> &nbsp; <a href=""> 
                                                <i class="fa fa-arrow-circle-right"></i>
                                                 Forward</a>
                                            </td>
                                        </tr>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <?php  
                        }
                        else
                        {
                        ?>
                        <div class="table-responsive">
                            <table class="table" id="chat-table">
                                <tbody>
                                <?php
                                $loginUserEmail = 'hadiniazi8009@gmail.com';
                                $sql = "SELECT * from mails";
                                $result = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_array($result)){
                                    if($row['mail_from'] === $loginUserEmail){
                                        $subject = $row['subject'];
                                        $dateTime = $row['date_time'];
                                        $seen = $row['is_seen']; 
                                        $mailId = $row['id']; 
                                ?>
                                <tr style="background-color:#F6F6F4">
                                <td class="subject">
                                    <a href="<?php echo 'sent.php?id='.$row['id']; ?>"> 
                                        <?php if(isset($subject)) { echo $subject; } ?> 
                                    </a>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <a style="color:red" href="<?php echo 'action.php?sentMailId='. $mailId ?>">
                                     <i class="fa fa-trash"></i>
                                    </a>

                                </td>
                                <td class="time">
                                    <?php
                                    if (isset($dateTime)) {
                                         echo date('d-m-Y H:i:A', strtotime($dateTime));
                                      }  }  
                                    ?>
                                </td>
                                </tr>
                                <?php } ?>
                               
                            </tbody></table>
                        </div>

                    <?php } ?>

                                            
                    </div>
                    <!-- END INBOX CONTENT -->
                    
                    <!-- BEGIN COMPOSE MESSAGE -->
                    <?php include('compose.php') ?>
                    <!-- END COMPOSE MESSAGE -->
                </div>
            </div>
        </div>
    </div>
    <!-- END INBOX -->
</div>
</div>


<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</body>
</html>