<?php
include_once('../../controller/cThongKe.php');
$p = new cThongKe();

$alert = "";
$hasData = false; // Check if data exists
$doanhThu = [];
$jsonDoanhThu = "";

if (isset($_REQUEST['submit'])) {
    $startDate = $_REQUEST['startDate'] ?? null;
    $endDate = $_REQUEST['endDate'] ?? null;
    $khoa = $_REQUEST['khoa'] ?? "0";
    $loaiTG = $_REQUEST['loaiTG'] ?? "0";
    $khoangTG = $_REQUEST['timeRange'] ?? null;
    $year = $_REQUEST['year'] ?? null;

    // Validate input
    if ($loaiTG == "0") {
        $alert = 'Vui lòng chọn loại thời gian.';
    } elseif ($loaiTG == "1" && $khoangTG == "4") {
        if (empty($startDate) || empty($endDate)) {
            $alert = 'Vui lòng chọn đầy đủ thời gian bắt đầu và kết thúc.';
        } elseif ($startDate > $endDate) {
            $alert = 'Thời gian bắt đầu không thể lớn hơn thời gian kết thúc.';
        }
    } elseif (($loaiTG == "2" || $loaiTG == "3") && empty($year)) {
        $alert = 'Vui lòng chọn năm.';
    }

    if (!empty($alert)) {
        echo "<script>alert('$alert'); window.location.href='thongkedoanhthutheokhoa.php';</script>";
        return;
    }

    // Call revenue statistics function
    $result = $p->thongKeDoanhThuTheoKhoa($khoa, $loaiTG, $khoangTG, $startDate, $endDate, $year);
    $doanhThu = $result['result'] ?? [];
    $jsonDoanhThu = $result['json'] ?? "";
    $hasData = !empty($doanhThu);

    if (!$hasData) {
        $alert = "Chưa có dữ liệu thống kê trong khoảng thời gian này.";
        echo "<script>alert('$alert'); window.location.href='thongkedoanhthutheokhoa.php';</script>";
        return;
    }

}

?>

<!DOCTYPE html>
<html lang="en">
    
    <!--Head Code-->
    <?php include("../../assets/inc/head.php");?>

    <body>

        <!-- Begin page -->
        <div id="wrapper">

            <!-- Topbar Start -->
            <?php include('../../assets/inc/nav.php');?>
            <!-- end Topbar -->

            <!-- ========== Left Sidebar Start ========== -->
            <div class="left-side-menu">

                <div class="slimscroll-menu">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">

                        <ul class="metismenu" id="side-menu">
                            <li>
                                <a href="index.php">
                                    <i class="fe-airplay"></i>
                                    <span> Dashboard </span>
                                </a>
                                
                            </li>

                            <li>
                                <a href="thongkedoanhthu.php">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Thống kê doanh thu</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <!-- <li>
                                        <a href="thongketongdoanhthu.php">Tổng doanh thu</a>
                                    </li> -->
                                    <li>
                                        <a href="thongkedoanhthutheoloaithoigian.php">Theo loại thời gian</a>
                                    </li>
                                    <li>
                                        <a href="thongkedoanhthutheokhoa.php">Theo khoa</a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="phanphongkham.php">
                                    <i class="mdi mdi-hospital-building"></i>
                                    <span>Phân số thứ tự, phòng khám</span>
                                    <span class="menu-arrow"></span>
                                </a>
                                
                            </li>
                        </ul>

                    </div>
                    <!-- End Sidebar -->

                    <div class="clearfix"></div>

                </div>
                <!-- Sidebar -left -->

            </div>
            <!-- Left Sidebar End -->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">

                    <!-- Start Content-->
                    <div class="container-fluid">
                        
                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    
                                    <h4 class="page-title">THỐNG KÊ DOANH THU</h4>
                                </div>
                            </div>
                        </div>     
                        <!-- end page title --> 

                        <hr style="border-color: black;">
                        
                        <div class="row mb-2">
                            <div class="col-12 text-center">
                                <a href="thongkedoanhthutheoloaithoigian.php" class="btn btn-success mx-2">THEO LOẠI THỜI GIAN</a>
                                <a href="thongkedoanhthutheokhoa.php" class="btn btn-danger mx-2">THEO KHOA</a>
                            </div>
                        </div>

                        <hr style="border-color: black;">

                        <form class="mb-3" method="POST" action="">
                            <div class="row">
                                
                                <div class="col-md-6">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-md-4 text-start">
                                            <label for="khoa" class="form-label">KHOA</label>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-select form-control" id="khoa" name="khoa">
                                                <option value="0" selected>Tất cả khoa</option>
                                                <?php
                                                    include_once("../../controller/cKhoa.php");
                                                    $p = new cKhoa();
                                                    $tbl = $p->layDSKhoa();

                                                    if (!$tbl) {
                                                        echo "<option>Không thể kết nối</option>";
                                                    } elseif ($tbl == -1) {
                                                        echo "<option>Chưa có dữ liệu</option>";
                                                    } else {
                                                        while ($row = $tbl->fetch_assoc()) {
                                                            if (isset($_POST['submit']) && $_POST['khoa'] == $row['maKhoa']) {
                                                                echo "<option value='{$row["maKhoa"]}' $selected>{$row["tenKhoa"]}</option>";
                                                            }else{
                                                                echo "<option value='{$row["maKhoa"]}'>{$row["tenKhoa"]}</option>";
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-md-4 text-start">
                                            <label for="loaiTG" class="form-label">LOẠI THỜI GIAN</label>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-select form-control" id="loaiTG" name="loaiTG">
                                                <option value="0">Chọn loại thời gian</option>
                                                <option value='1'>Ngày</option>
                                                <option value='2'>Tháng</option>
                                                <option value='3'>Quý</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class = "row">
                                <div class = "col-md-6">
                                    <div class="form-group time-range d-none">
                                        <div class="row">
                                            <div class="col-md-4 text-start">
                                                <label for="timeRange" class="form-label">KHOẢNG THỜI GIAN</label>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-select form-control" id="timeRange" name="timeRange">
                                                    <option value='1'>Hôm nay</option>
                                                    <option value='2'>Trong 7 ngày</option>
                                                    <option value='3'>Trong tháng</option>
                                                    <option value='4'>Tùy chọn</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group year d-none">
                                        <div class="row">
                                            <div class="col-md-4 text-start">
                                                <label for="year">NĂM</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="yearpicker" name="year" value="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group custom-date-range d-none mt-3">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="startDate">NGÀY BẮT ĐẦU</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" id="startDate" name="startDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" min="2023-03-08" max="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="endDate">NGÀY KẾT THÚC</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" id="endDate" name="endDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" min="2023-03-08" max="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="row text-center mt-3">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary mx-2" id="submit" name="submit">XÁC NHẬN</button>
                                </div>
                            </div>
                        </form>

                        <hr style="border-color: black;">

                        <h4 class="header-title mb-3">DOANH THU BỆNH VIỆN</h4>
                        <div class="row">
                            <!--Thông báo-->
                            <?php if (!empty($alert)): ?>
                                <div class="alert alert-warning text-center" role="alert">
                                    <?php echo htmlspecialchars($alert); ?>
                                </div>
                            <?php endif; ?>
                            <!-- Bảng doanh thu -->
                            <div class="card-box">
                                <div class="table-responsive">
                                    <?php if (!empty($doanhThu)): ?>
                                        <table class='table table-borderless table-hover table-centered m-0'>
                                            <thead class='thead-light'>
                                                <tr>
                                                    <th>Thời gian</th>
                                                    <?php
                                                        if ($khoa == "0") { // All departments selected
                                                            foreach ($tbl as $row) { 
                                                                echo "<th>{$row['tenKhoa']}</th>";
                                                            }
                                                        } else {
                                                            echo "<th>{$doanhThu[0]['tenKhoa']}</th>"; // Only show selected department
                                                        }
                                                    ?>
                                                    <th>Tổng cộng</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($doanhThu as $row): ?>
                                                    <tr>
                                                        <?php
                                                            // Display time based on selected period type
                                                            if ($loaiTG == "1") { // By day
                                                                echo "<td>" . htmlspecialchars($row['ngayKham']) . "</td>";
                                                            } elseif ($loaiTG == "2") { // By month
                                                                echo "<td>Tháng " . htmlspecialchars($row['thang']) . " / " . htmlspecialchars($row['nam']) . "</td>";
                                                            } elseif ($loaiTG == "3") { // By quarter
                                                                echo "<td>Quý " . htmlspecialchars($row['quy']) . " / " . htmlspecialchars($row['nam']) . "</td>";
                                                            }

                                                            // Initialize the total revenue for this row (for all departments)
                                                            $totalRevenue = 0;

                                                            // Display revenue by department
                                                            if ($khoa == "0") { // All departments
                                                                foreach ($tbl as $department) {
                                                                    $deptName = $department['tenKhoa'];
                                                                    // If revenue for this department exists, show it; otherwise, show 0
                                                                    $departmentRevenue = isset($row['doanhThu'][$deptName]) ? $row['doanhThu'][$deptName] : 0;
                                                                    echo "<td>" . number_format($departmentRevenue, 0) . " VND</td>";

                                                                    // Add to the total revenue for this row (for "Tổng cộng" column)
                                                                    $totalRevenue += $departmentRevenue;
                                                                }
                                                            } else { // Specific department
                                                                $departmentRevenue = $row['doanhThu']; // Only one department's revenue
                                                                echo "<td>" . number_format($departmentRevenue, 0) . " VND</td>";

                                                                // Add the department's revenue to the total
                                                                $totalRevenue += $departmentRevenue;
                                                            }

                                                            // Display the total revenue for all departments in this time period
                                                            echo "<td>" . number_format($totalRevenue, 0) . " VND</td>";
                                                        ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php else: ?>
                                    <?php endif; ?>
                                </div>
                            </div>                              
                        </div>

                        <div class="row">
                            <!-- Revenue chart -->
                            <div class="card-box">
                                <div class="table-responsive">
                                    <canvas id="revenueChart" width="600" height="400"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- jQuery Library -->
                        <script src="../../assets/js/vendor/jquery-3.7.1.min.js"></script>

                        <!-- Flatpickr CSS -->
                        <link rel="stylesheet" href="../../assets/libs/flatpickr/flatpickr.min.css">

                        <!-- Flatpickr JS -->
                        <script src="../../assets/libs/flatpickr/flatpickr.min.js"></script>

                        <script>
                            $(document).ready(function() {
                                $("#loaiTG").change(function() {
                                    var selectedType = $(this).val();
                                    if (selectedType == "1") { // Ngày
                                        $('.time-range').removeClass('d-none');
                                        $('.year').addClass('d-none');
                                    } else if (selectedType == "2" || selectedType == "3") { // Tháng hoặc Quý
                                        $('.time-range').addClass('d-none');
                                        $('.year').removeClass('d-none');
                                        $('.custom-date-range').addClass('d-none');
                                        $(function() {  
                                            $('.yearpicker').yearpicker();
                                        });

                                        $(function() {  
                                            $('.yearpicker').yearpicker({
                                            autoHide: true,
                                            year: null,
                                            startYear: null,
                                            endYear: null,
                                            itemTag: 'li',
                                            selectedClass: 'selected',
                                            disabledClass: 'disabled',
                                            hideClass: 'hide',
                                            highlightedClass: 'highlighted'
                                            });
                                        });
                                    }else if(selectedType == "0"){
                                        $('.time-range').addClass('d-none');
                                        $('.year').addClass('d-none');
                                        $('.custom-date-range').addClass('d-none');
                                    }
                                });

                                $('#timeRange').on('change', function() {
                                    if ($(this).val() == '4') {
                                        $('.custom-date-range').removeClass('d-none');
                                    } else {
                                        $('.custom-date-range').addClass('d-none');
                                    }
                                });

                                $("#startDate, #endDate").flatpickr({
                                    dateFormat: "Y-m-d",  // Format to Year-Month-Day
                                    enableTime: false     // No time selection
                                });
                                
                            });
                        </script>

                        <!-- Script to draw charts with chart.js -->
                        <script src="../../assets/js/Chart.min.js"></script>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                            const ctx = document.getElementById('revenueChart').getContext('2d');
                            const doanhThu = <?php echo $jsonDoanhThu ?? '[]'; ?>;

                            if (doanhThu.length > 0) {
                                // Tạo danh sách nhãn (labels) tùy theo loại thời gian (loaiTG)
                                const labels = [];
                                const seenLabels = new Set(); // Set để lưu trữ các nhãn đã được thêm

                                if ("<?= $loaiTG ?>" === "1") { // Loại TG = Ngày
                                    doanhThu.forEach(item => {
                                        if (!seenLabels.has(item.ngayKham)) {
                                            labels.push(item.ngayKham);
                                            seenLabels.add(item.ngayKham);
                                        }
                                    });
                                } else if ("<?= $loaiTG ?>" === "2") { // Loại TG = Tháng
                                    doanhThu.forEach(item => {
                                        const label = `Tháng ${item.thang} / ${item.nam}`;
                                        if (!seenLabels.has(label)) {
                                            labels.push(label);
                                            seenLabels.add(label);
                                        }
                                    });
                                } else if ("<?= $loaiTG ?>" === "3") { // Loại TG = Quý
                                    doanhThu.forEach(item => {
                                        const label = `Quý ${item.quy} / ${item.nam}`;
                                        if (!seenLabels.has(label)) {
                                            labels.push(label);
                                            seenLabels.add(label);
                                        }
                                    });
                                }

                                // Khởi tạo đối tượng departments
                                const departments = {};
                                doanhThu.forEach(item => {
                                    if (!departments[item.tenKhoa]) {
                                        departments[item.tenKhoa] = { 
                                            label: item.tenKhoa, 
                                            data: [], 
                                            backgroundColor: getRandomColor() 
                                        };
                                    }
                                    departments[item.tenKhoa].data.push(item.doanhThu);
                                });

                                // Chuyển departments thành mảng datasets
                                const datasets = Object.values(departments);

                                // Khởi tạo biểu đồ với Chart.js
                                new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: labels,
                                        datasets: datasets
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: { position: 'top' },
                                            tooltip: {
                                                callbacks: {
                                                    label: context => `${context.dataset.label}: ${context.raw} VND`
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                title: { display: true, text: 'Doanh thu (VND)' }
                                            },
                                            x: {
                                                title: { display: true, text: 'Thời gian' } // Nhãn thời gian phù hợp cho cả ngày, tháng, quý
                                            }
                                        }
                                    }
                                });

                                // Hàm tạo màu ngẫu nhiên cho từng khoa
                                function getRandomColor() {
                                    const hue = Math.floor(Math.random() * 360);
                                    return `hsl(${hue}, 70%, 60%)`;
                                }
                            } else {
                                console.log("No revenue data available");
                            }
                        });

                        </script>

                    </div> <!-- container -->

                </div> <!-- content -->

            </div>

        <!-- Vendor js -->
        <script src="../../assets/js/vendor.min.js"></script>
        <!-- Plugins js-->
        <script src="../../assets/libs/flatpickr/flatpickr.min.js"></script>
        <script src="../../assets/libs/jquery-knob/jquery.knob.min.js"></script>
        <script src="../../assets/libs/jquery-sparkline/jquery.sparkline.min.js"></script>
        <script src="../../assets/libs/flot-charts/jquery.flot.js"></script>
        <script src="../../assets/libs/flot-charts/jquery.flot.time.js"></script>
        <script src="../../assets/libs/flot-charts/jquery.flot.tooltip.min.js"></script>
        <script src="../../assets/libs/flot-charts/jquery.flot.selection.js"></script>
        <script src="../../assets/libs/flot-charts/jquery.flot.crosshair.js"></script>

        <!-- Dashboar 1 init js-->
        <script src="../../assets/js/pages/dashboard-1.init.js"></script>

        <!-- App js-->
        <script src="../../assets/js/app.min.js"></script>

        <!--Chart js-->
        <script src="../../assets/js/Chart.min.js"></script>

        <!--Jquery js-->
        <script src="../../assets/js/vendor/jquery-3.7.1.min.js"></script>
        <script src="../../assets/libs/YearPicker-master/docs/yearpicker.js" async></script>
        
    </body>

</html>