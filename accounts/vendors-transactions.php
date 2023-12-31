<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: login.php");
}

if (isset($_GET['logout']) || strtolower($_SESSION["role"]) != "accounts") {
    session_destroy();
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    header('Location: ../login.php');
}
?>
<?php
require_once('../bootstrap.php');

use Src\Controller\AdminController;

$admin = new AdminController();
require_once('../inc/page-data.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("../inc/head.php") ?>
    <style>
        ._textD {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php require_once("../inc/header.php") ?>

    <?php require_once("../inc/sidebar.php") ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Forms Sale</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Vendors Statistics</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">

                        <div class="filter">
                            <span class="icon download-file" id="excelFileDownload" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Export as Excel file">
                                <img src="../assets/img/icons8-microsoft-excel-2019-48.png" alt="Download as Excel file" style="cursor:pointer;width: 24px;">
                            </span>
                            <span class="icon download-file" id="pdfFileDownload" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Download as PDF file">
                                <img src="../assets/img/icons8-pdf-48.png" alt="Download as PDF file" style="width: 24px;cursor:pointer;">
                            </span>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">Purchases</h5>
                            <hr>
                            <!-- Left side columns -->

                            <div class="mt-4 row">
                                <div class="col-6 col-md-6 col-sm-12 mt-2">
                                    <div class="row">
                                        <h6 style="font-weight: 600;">Total Purchase: <span id="totalPurchase"></span></h6>
                                        <h6 style="font-weight: 600;">Total Amount: <span id="totalAmount"></span></h6>
                                        <div id="alert-output"></div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-6 col-sm-12 mt-2">
                                    <form action="" method="post">
                                        <div style="margin-top: 50px !important; display: flex; justify-content: space-between; align-content:baseline">
                                            <div>
                                                <label for="from-date" class="form-label">Filter By</label>
                                                <select name="report-by" id="report-by" class="form-select">
                                                    <option value="" selected disabled>Choose</option>
                                                    <option value="PayMethod">Payment Method</option>
                                                    <option value="Vendors">Vendors</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label for="from-date" class="form-label">From (Date)</label>
                                                <input type="date" name="from-date" id="from-date" class="form-control">
                                            </div>

                                            <div>
                                                <label for="to-date" class="form-label">To (Date)</label>
                                                <input type="date" name="to-date" id="to-date" class="form-control">
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>

                            <div style="margin-top: 50px !important">
                                <table class="table table-borderless table-striped table-hover" id="dataT">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">S/N</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Total Sold</th>
                                            <th scope="col">(Total) Amount</th>
                                            <th scope="col"></th>
                                        </tr>
                                    </thead>

                                    <tbody id="saleGroupTbody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- End Left side columns -->

            <!-- Purchase info Modal -->
            <div class="modal fade" id="salesInfoModal" tabindex="-1" aria-labelledby="salesInfoModal" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="salesInfoModalTitle">Purchase Information</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-4 row">
                                <div class="mb-3 col-4">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon3">Vendor: </span>
                                        <input disabled type="text" class="form-control _textD" id="p-transID" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="mb-3 col-4">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon3">Total Sold: </span>
                                        <input disabled type="text" class="form-control _textD" id="p-admisP" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="mb-3 col-4">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon3">Total Amount: </span>
                                        <input disabled type="text" class="form-control _textD" id="p-admisP" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                            </div>
                            <table class="table table-borderless table-striped table-hover" id="dataT">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">S/N</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Country</th>
                                        <th scope="col">Payment Method</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody id="saleReportTbody">
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side columns -->
            <!-- End Right side columns -->

        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>
    <script>
        $(document).ready(function() {

            var triggeredBy = 0;

            // when 
            $(".form-select, .form-control").change("blur", function(e) {
                e.preventDefault();
                $("#reportsForm").submit();
            });

            $("#report-by").change("blur", function(e, d) {
                e.preventDefault();
                triggeredBy = 1;

                let data = {
                    _data: $(this).val()
                }

                // Executes when download is click, either for excel or pdf download
                if (d == "pdfFileDownload" || d == "excelFileDownload") {
                    $.ajax({
                        type: "POST",
                        url: "../endpoint/download-file",
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.success) {
                                window.open("../download-pdf.php?w=" + d, "_blank");
                            } else {
                                $("#alert-output").html('');
                                $("#alert-output").html(
                                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    '<i class="bi bi-info-circle me-1"></i>' + result.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>'
                                );
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });

                }

                // Executes when purchase data is fetched
                else {
                    $.ajax({
                        type: "POST",
                        url: "../endpoint/group-sales-report",
                        data: data,
                        success: function(result) {
                            console.log(result);

                            if (result.success) {
                                $("#saleGroupTbody").html('');
                                let totalP = 0;
                                let totalA = 0.0;
                                $.each(result.message, function(index, value) {
                                    totalP += parseInt(value.total_num_sold);
                                    totalA += parseFloat(value.total_amount_sold);
                                    $("#saleGroupTbody").append(
                                        '<tr>' +
                                        '<td>' + (index + 1) + '</td>' +
                                        '<td>' + value.title + '</td>' +
                                        '<td>' + value.total_num_sold + '</td>' +
                                        '<td>' + value.total_amount_sold + '</td>' +
                                        '<td>' +
                                        '<button id="' + value.id + '" class="btn btn-xs btn-primary openSalesInfo" data-bs-toggle="modal" data-bs-target="#salesInfoModal">View</button>' +
                                        '</td>' +
                                        '</tr>'
                                    );
                                });
                                $("#totalPurchase #totalAmount").text('');
                                $("#totalPurchase").text(totalP);
                                $("#totalAmount").text("GHS " + totalA.toFixed(2));
                            } else {
                                $("#alert-output").html('');
                                $("#alert-output").html(
                                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    '<i class="bi bi-info-circle me-1"></i>' + result.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>'
                                );
                            }

                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
            });

            $(document).on("click", ".openSalesInfo", function() {
                triggeredBy = 2;
                let data = {
                    _dataI: $(this).attr("id"),
                    _dataT: $("#report-by").val(),
                }

                $.ajax({
                    type: "POST",
                    url: "../endpoint/group-sales-report-list",
                    data: data,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("#saleReportTbody").html('');
                            $.each(result.message, function(index, value) {
                                $("#saleReportTbody").append(
                                    '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + value.first_name + ' ' + value.last_name + '</td>' +
                                    '<td>' + value.country_name + '</td>' +
                                    '<td>' + value.payment_method + '</td>' +
                                    '<td>' + value.added_at + '</td>' +
                                    '</tr>'
                                );
                            });
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $("#sendPurchaseInfoForm").on("submit", function(e) {
                e.preventDefault();
                triggeredBy = 3;
                $.ajax({
                    type: "POST",
                    url: "../endpoint/send-purchase-info",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result);

                        $("#msgContent").text(result.message);
                        if (result.success) {
                            $(".infoFeed").removeClass("alert-danger").addClass("alert-success").toggle();
                        } else {
                            $(".infoFeed").removeClass("alert-success").addClass("alert-danger").toggle();
                        }

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $(document).on({
                ajaxStart: function() {
                    if (triggeredBy == 3) $("#sendTransIDBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> processing...');
                    else $("#alert-output").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    if (triggeredBy == 3) $("#sendTransIDBtn").prop("disabled", false).html('Send application login info');
                    else $("#alert-output").html('');
                }
            });

            $(document).on("click", ".download-file", function() {
                let data = {
                    actionType: $(this).attr("id")
                }
                $("#reportsForm").trigger("submit", $(this).attr("id"));
            });

        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on({
                ajaxStart: function() {
                    // Show full page LoadingOverlay
                    $.LoadingOverlay("show");
                },
                ajaxStop: function() {
                    // Hide it after 3 seconds
                    $.LoadingOverlay("hide");
                }
            });
        });
    </script>

</body>

</html>