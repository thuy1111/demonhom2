<?php
// Quản lý phiếu khám bệnh
include_once("connect.php");

class mPhieuKhamBenh
{
    // Lấy danh sách phiếu khám bệnh (có tìm kiếm)
    public function layDSPKB($keyword = "", $maBenhNhan = "")
    {
        $p = new clsKetNoi();
        $conn = $p->moketnoi();
        $conn->set_charset("utf8");
        if ($conn) {
            $str = "SELECT pkb.*, bn.hoTen as tenBenhNhan, nv.hoTen as tenNhanVien 
                    FROM phieukhambenh pkb
                    JOIN benhnhan bn ON pkb.maBenhNhan = bn.maBenhNhan
                    JOIN nhanvien nv ON pkb.maNhanVien = nv.maNhanVien
                    WHERE 1=1";
            $params = array();
            $types = "";

            if ($keyword != "") {
                $str .= " AND (pkb.maPhieuKhamBenh LIKE ? OR bn.hoTen LIKE ? OR nv.hoTen LIKE ? OR pkb.ngayKham LIKE ?)";
                $search = "%$keyword%";
                $params = array_merge($params, array($search, $search, $search, $search));
                $types .= "ssss";
            }

            if ($maBenhNhan != "") {
                $str .= " AND pkb.maBenhNhan = ?";
                $params[] = $maBenhNhan;
                $types .= "s";
            }

            $stmt = $conn->prepare($str);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            $p->dongketnoi($conn);
            return $result;
        } else {
            return false;
        }
    }

    // Lấy chi tiết phiếu khám bệnh theo mã phiếu
    public function layChiTietPKB($maPhieuKhamBenh)
    {
        $p = new clsKetNoi();
        $conn = $p->moketnoi();
        $conn->set_charset("utf8");
        if ($conn) {
            $str = "SELECT pkb.*, bn.hoTen as tenBenhNhan, nv.hoTen as tenNhanVien
                    FROM phieukhambenh pkb
                    JOIN benhnhan bn ON pkb.maBenhNhan = bn.maBenhNhan
                    JOIN nhanvien nv ON pkb.maNhanVien = nv.maNhanVien
                    WHERE pkb.maPhieuKhamBenh = ?";
            $stmt = $conn->prepare($str);
            $stmt->bind_param("s", $maPhieuKhamBenh);
            $stmt->execute();
            $result = $stmt->get_result();
            $p->dongketnoi($conn);
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }
}
?>