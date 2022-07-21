<?php

session_start();

//koneksi
$c = mysqli_connect('localhost', 'root', '', 'db_uas_rafi22');

//login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check = mysqli_query($c, "SELECT * FROM user WHERE username='$username' and password='$password'");
    $hitung = mysqli_num_rows($check);

    if ($hitung > 0) {
        $_SESSION['login'] = 'true';
        header('location:index.php');
    } else {
        echo '<script>alert("Username ayau Password salah");
        window.location.href="login.php"
        </script>';
    }
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $insert = mysqli_query($c, "INSERT INTO user (username,password) VALUES ('$username','$password')");
    if ($insert) {
        echo '<script>alert("Berhasil menambah akun");
        window.location.href="login.php"
        </script>';
    } else {
        echo '<script>alert("Gagal menambah akun");
        window.location.href="register.php"
        </script>';
    }
}

if (isset($_POST['tambahbarang'])) {
    $namaproduk = $_POST['namaproduk'];
    $deskripsi = $_POST['deskripsi'];
    $stock = $_POST['stock'];
    $harga = $_POST['harga'];

    $insert = mysqli_query($c, "INSERT INTO produk (namaproduk,deskripsi,harga,stock) VALUES ('$namaproduk','$deskripsi','$harga','$stock')");

    if ($insert) {
        header('location:stock.php');
    } else {
        echo '<script>alert("Gagal menambah barang baru");
        window.location.href="stock.php"
        </script>';
    }
}

if (isset($_POST['tambahpelanggan'])) {
    $namapelanggan = $_POST['namapelanggan'];
    $notelp = $_POST['notelp'];
    $alamat = $_POST['alamat'];

    $insert = mysqli_query($c, "INSERT INTO pelanggan (namapelanggan,notelp,alamat) VALUES ('$namapelanggan','$notelp','$alamat')");

    if ($insert) {
        header('location:pelanggan.php');
    } else {
        echo '<script>alert("Gagal menambah member baru");
        window.location.href="pelanggan.php"
        </script>';
    }
}
if (isset($_POST['tambahpesanan'])) {
    $idpelanggan = $_POST['idpelanggan'];

    $insert = mysqli_query($c, "INSERT INTO pesanan (idpelanggan) VALUES ('$idpelanggan')");

    if ($insert) {
        header('location:index.php');
    } else {
        echo '<script>alert("Gagal menambah pesanan baru");
        window.location.href="index.php"
        </script>';
    }
}
if (isset($_POST['tambahproduk'])) {
    $idproduk = $_POST['idproduk'];
    $idp = $_POST['idp']; //idpesanan
    $qty = $_POST['qty'];

    $hitung1 = mysqli_query($c, "SELECT * FROM produk WHERE idproduk='$idproduk'");
    $hitung2 = mysqli_fetch_array($hitung1);
    $stocksekarang = $hitung2['stock'];
    if ($stocksekarang >= $qty) {

        $selisih = $stocksekarang - $qty;

        $insert = mysqli_query($c, "INSERT INTO detailpesanan (idpesanan,idproduk,qty) VALUES ('$idp','$idproduk','$qty')");
        $update = mysqli_query($c, "UPDATE produk SET stock='$selisih' WHERE idproduk='$idproduk'");
        if ($insert && $update) {
            header('location:view.php?idp' . $idp);
        } else {
            echo '<script>alert("Gagal menambah pesanan baru");
        window.location.href="view.php?idp' . $idp . '"
        </script>';
        }
    } else {
        echo '<script>alert("Stock barang tidak cukup");
        window.location.href="view.php?idp' . $idp . '"
        </script>';
    }
}

if (isset($_POST['barangmasuk'])) {
    $idproduk = $_POST['idproduk'];
    $qty = $_POST['qty'];

    $cekstock = mysqli_query($c, "SELECT * FROM produk WHERE idproduk='$idproduk'");
    $cekstock2 = mysqli_fetch_array($cekstock);
    $stocksekarang = $cekstock2['stock'];

    $newstock = $stocksekarang + $qty;
    $insert = mysqli_query($c, "INSERT INTO masuk (idproduk,qty) VALUES ('$idproduk','$qty')");
    $update = mysqli_query($c, "UPDATE produk SET stock='$newstock' WHERE idproduk='$idproduk'");

    if ($insert && $update) {
        header('location:masuk.php');
    } else {
        echo '<script>alert("Gagal");
        window.location.href="masuk.php"
        </script>';
    }
}

if (isset($_POST['hapusprodukpesanan'])) {
    $idp = $_POST['idp'];
    $idpr = $_POST['idpr'];
    $idorder = $_POST['idorder'];

    $cek1 = mysqli_query($c, "SELECT * FROM detailpesanan WHERE iddetailpesanan='$idp'");
    $cek2 = mysqli_fetch_array($cek1);
    $qtysekarang = $cek2['qty'];

    $cek3 = mysqli_query($c, "SELECT * FROM produk WHERE idproduk='$idpr'");
    $cek4 = mysqli_fetch_array($cek3);
    $stocksekarang = $cek4['stock'];

    $hitung = $stocksekarang + $qtysekarang;
    $update = mysqli_query($c, "UPDATE produk SET stock='$hitung' WHERE idproduk='$idpr'");
    $hapus = mysqli_query($c, "DELETE FROM detailpesanan WHERE idproduk='$idpr' AND iddetailpesanan='$idp'");

    if ($update && $hapus) {
        header('location:view.php?idp' . $idorder);
    } else {
        echo '<script>alert("Gagal menghapus barang");
        window.location.href="view.php?idp' . $idorder . '"
        </script>';
    }
}

if (isset($_POST['editbarang'])) {
    $np = $_POST['namaproduk'];
    $desc = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $idp = $_POST['idp']; //idproduk

    $query = mysqli_query($c, "UPDATE produk SET namaproduk='$np', deskripsi='$desc', harga='$harga' WHERE idproduk='$idp'");

    if ($query) {
        header('location:stock.php');
    } else {
        echo '<script>alert("Gagal");
        window.location.href="stock.php"
        </script>';
    }
}

if (isset($_POST['hapusbarang'])) {
    $idp = $_POST['idp']; //idproduk
    $query = mysqli_query($c, "DELETE FROM produk WHERE idproduk='$idp'");

    if ($query) {
        header('location:stock.php');
    } else {
        echo '<script>alert("Gagal");
        window.location.href="stock.php"
        </script>';
    }
}

if (isset($_POST['editpelanggan'])) {
    $np = $_POST['namapelanggan'];
    $nt = $_POST['notelp'];
    $alamat = $_POST['alamat'];
    $idpl = $_POST['idpl']; //idpelanggan

    $query = mysqli_query($c, "UPDATE pelanggan SET namapelanggan='$np', notelp='$nt', alamat='$alamat' WHERE idpelanggan='$idpl'");

    if ($query) {
        header('location:pelanggan.php');
    } else {
        echo '<script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>';
    }
}

if (isset($_POST['hapuspelanggan'])) {
    $idpl = $_POST['idpl']; //idpelanggan
    $query = mysqli_query($c, "DELETE FROM pelanggan WHERE idpelanggan='$idpl'");

    if ($query) {
        header('location:pelanggan.php');
    } else {
        echo '<script>alert("Gagal");
        window.location.href="pelanggan.php"
        </script>';
    }
}

if (isset($_POST['editbarangmasuk'])) {
    $qty = $_POST['qty'];
    $idm = $_POST['idm']; //idmasuk
    $idp = $_POST['idp']; //idproduk

    $cek = mysqli_query($c, "SELECT * FROM masuk WHERE idmasuk='$idm'");
    $cek2 = mysqli_fetch_array($cek);
    $qtysekarang = $cek2['qty'];

    $cekstock = mysqli_query($c, "SELECT * FROM produk WHERE idproduk='$idp'");
    $cekstock2 = mysqli_fetch_array($cekstock);
    $stocksekarang = $cekstock2['stock'];

    if ($qty >= $qtysekarang) {
        $selisih = $qty - $qtysekarang;
        $newstock = $stocksekarang + $selisih;

        $query1 = mysqli_query($c, "UPDATE masuk SET qty='$qty' WHERE idmasuk='$idm'");
        $query2 = mysqli_query($c, "UPDATE produk SET stock='$newstock' WHERE idproduk='$idp'");

        if ($query1 && $query2) {
            header('location:masuk.php');
        } else {
            echo '<script>alert("Gagal");
        window.location.href="masuk.php"
        </script>';
        }
    } else {
        $selisih = $qtysekarang - $qty;
        $newstock = $stocksekarang - $selisih;

        $query1 = mysqli_query($c, "UPDATE masuk SET qty='$qty' WHERE idmasuk='$idm'");
        $query2 = mysqli_query($c, "UPDATE produk SET stock='$newstock' WHERE idproduk='$idp'");

        if ($query1 && $query2) {
            header('location:masuk.php');
        } else {
            echo '<script>alert("Gagal");
            window.location.href="masuk.php"
            </script>';
        }
    }
}

if (isset($_POST['hapusbarangmasuk'])) {
    $idm = $_POST['idm']; //idmasuk
    $idp = $_POST['idp']; //idproduk

    $cek = mysqli_query($c, "SELECT * FROM masuk WHERE idmasuk='$idm'");
    $cek2 = mysqli_fetch_array($cek);
    $qtysekarang = $cek2['qty'];

    $cekstock = mysqli_query($c, "SELECT * FROM produk WHERE idproduk='$idp'");
    $cekstock2 = mysqli_fetch_array($cekstock);
    $stocksekarang = $cekstock2['stock'];

    $newstock = $stocksekarang - $qtysekarang;

    $query1 = mysqli_query($c, "DELETE FROM masuk WHERE idmasuk='$idm'");
    $query2 = mysqli_query($c, "UPDATE produk SET stock='$newstock' WHERE idproduk='$idp'");

    if ($query1 && $query2) {
        header('location:masuk.php');
    } else {
        echo '<script>alert("Gagal");
            window.location.href="masuk.php"
            </script>';
    }
}

if (isset($_POST['hapuspesanan'])) {
    $idp = $_POST['idp']; //idpesanan

    $cekdata = mysqli_query($c, "SELECT * FROM detailpesanan dp WHERE idpesanan='$idp'");
    while ($data = mysqli_fetch_array($cekdata)) {
        $qty = $data['qty'];
        $idproduk = $data['idproduk'];
        $iddp = $data['iddetailpesanan'];

        $cekstock = mysqli_query($c, "SELECT * FROM produk WHERE idproduk='$idproduk'");
        $cekstock2 = mysqli_fetch_array($cekstock);
        $stocksekarang = $cekstock2['stock'];

        $newstock = $stocksekarang+$qty;
        $update = mysqli_query($c, "UPDATE produk SET stock='$newstock' WHERE idproduk='$idproduk'");

        $delete = mysqli_query($c, "DELETE FROM detailpesanan WHERE iddetailpesanan='$iddp'");

    }

    $query = mysqli_query($c, "DELETE FROM pesanan WHERE idorder='$idp'");

    if ($update&&$delete&&$query) {
        header('location:index.php');
    } else {
        echo '<script>alert("Gagal");
        window.location.href="index.php"
        </script>';
    }
}

if (isset($_POST['editdetail'])) {
    $qty = $_POST['qty'];
    $iddp = $_POST['iddp']; //idmasuk
    $idpr = $_POST['idpr']; //idproduk
    $idp = $_POST['idp'];  //idpesanan

    $cek = mysqli_query($c, "SELECT * FROM detailpesanan WHERE iddetailpesanan='$iddp'");
    $cek2 = mysqli_fetch_array($cek);
    $qtysekarang = $cek2['qty'];

    $cekstock = mysqli_query($c, "SELECT * FROM produk WHERE idproduk='$idpr'");
    $cekstock2 = mysqli_fetch_array($cekstock);
    $stocksekarang = $cekstock2['stock'];

    if ($qty >= $qtysekarang) {
        $selisih = $qty - $qtysekarang;
        $newstock = $stocksekarang - $selisih;

        $query1 = mysqli_query($c, "UPDATE detailpesanan SET qty='$qty' WHERE iddetailpesanan='$iddp'");
        $query2 = mysqli_query($c, "UPDATE produk SET stock='$newstock' WHERE idproduk='$idpr'");

        if ($query1 && $query2) {
            header('location:view.php?idp'.$idp);
        } else {
            echo '<script>alert("Gagal");
        window.location.href="view.php?idp='.$idp.'"
        </script>';
        }
    } else {
        $selisih = $qtysekarang - $qty;
        $newstock = $stocksekarang + $selisih;

        $query1 = mysqli_query($c, "UPDATE detailpesanan SET qty='$qty' WHERE iddetailpesanan='$iddp'");
        $query2 = mysqli_query($c, "UPDATE produk SET stock='$newstock' WHERE idproduk='$idpr'");

        if ($query1 && $query2) {
            header('location:view.php?idp'.$idp);
        } else {
            echo '<script>alert("Gagal");
            window.location.href="view.php?idp='.$idp.'"
            </script>';
        }
    }
}
?>