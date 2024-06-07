<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Baptis</title>
    <style>
        .h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 0;
            text-align: center;
            margin: 0px;
            font-style: italic;
        }
        .a4 {
            width: 21cm;
            /* height: 29.7cm; */
            /* border: black solid 1px; */
            /* display: flex;
            justify-content: center;
            align-items: center; */
        }
        
        .a5 {
            width: 14cm;
            /* height: 20cm; */
            margin: 220px 2.5cm 0px;
            /* border: black solid 1px; */
            /* padding: 100px 70px; */
            /* text-align: center; */
        }
        .ayat {
            text-align: justify;
        }
        .no {
            font-size: larger;
            text-align: center;
            margin: 2px 0px 0px;
        }
        .item {
            font-size: large;
            text-align: center;
        }
        .under {
            text-decoration: underline;
        }
        .garis {
            width: 90%;
            height: 1.2px;
            background-color: black;
            position: absolute;
            top: 18px;
            left: 50px;
        }
        .garis-2 {
            width: 69%;
            height: 1.2px;
            background-color: black;
            position: absolute;
            top: 18px;
            left: 163px;
        }
        .garis-3 {
            width: 83.4%;
            height: 1.2px;
            background-color: black;
            position: absolute;
            top: 18px;
            left: 87px;
        }
        .garis-4 {
            width: 85.3%;
            height: 1.2px;
            background-color: black;
            position: absolute;
            top: 18px;
            left: 74px;
        }
        .garis-5 {
            width: 64%;
            height: 1.2px;
            background-color: black;
            position: absolute;
            top: 18px;
            left: 187px;
        }
        .garis-6 {
            width: 97%;
            height: 1.2px;
            background-color: black;
            position: absolute;
            top: 18px;
            left: 15px;
        }
        .garis-7 {
            width: 60%;
            position: absolute;
            top: 20px;
            left: 210px;
            text-align: center;
        }
        .garis-7 .p {
            width: 100%;
        }
        .garis-9 {
            width: 100%;
            height: 1.2px;
            margin: 0;
            background-color: black;
        }
        .garis-8 {
            width: 60%;
            height: 1.2px;
            background-color: black;
            position: absolute;
            top: 113px;
            left: 210px;
        }
        .form-1 p {
            position: absolute;
            top: 100px;
            left: 310px;
        }
        .form-1 .name {
            position: absolute;
            top: 80px;
            left: 210px;
            width: 60%;
            text-align: center;
        }
        .kotak {
            width: 75px;
            height: 100px;
            border: black solid 1.5px;
            position: absolute;
            top: 38px;
            left: 50px;
            font-size: small;
            text-align: center;
            align-content: center;
        }
        .form {
            position: relative;
            text-align: start !important;
        }
        .form-1 {
            position: relative;
            text-align: start !important;
            margin-top: 50px;
        }
        .form p {
            text-align: start !important;
            margin: 5px auto; 
        }
        .dalam {
            text-align: center;
        }
    </style>
</head>

<body>
  <div class="a4"> 
    <div class="a5">
        <p class="h1">SURAT BAPTIS</p>
        <p class="no"><b>No. <span class="under">{{ $data['no'] }}</span></b></p>
        <p class="ayat"><i>Dengan demikian kita telah dikuburkan bersama-sama dengan Dia oleh baptisan dalam kematian, supaya, sama seperti Kristus telah dibangkitkan dari antara orang mati oleh kemuliaan Bapa, demikian juga kita akan hidup dalam hidup yang baru. (Rm. 6:4)</i></p>
        <p class="item">Pada hari <u>{{ $cd[0] }}</u> tanggal <u>{{$cd[1]}}</u></p>
        <p class="dalam"><b>DALAM NAMA BAPA, ANAK DAN ROH KUDUS <br/>DALAM NAMA TUHAN YESUS KRISTUS<br/>TELAH DIBAPTISKAN SESUAI DENGAN FIRMAN TUHAN</b></p>
        <div class="form">
            <p>Nama : {{ $data['user']->name}}</p>
            <div class="garis"></div>
        </div>
        <div class="form">
            <p>Tempat / Tanggal Lahir : {{ $data['user']->place_of_birth }} / {{ $db[0] }} {{$db[1]}}</p>
            <div class="garis-2"></div>
        </div>
        <div class="form">
            <p>Nama Ayah : {{ $data['user']->father }}</p>
            <div class="garis-3"></div>
        </div>
        <div class="form">
            <p>Nama Ibu : {{ $data['user']->mother }}</p>
            <div class="garis-4"></div>
        </div>
        <div class="form">
            <p>Baptisan ini dilakukan oleh : {{ $data['who_baptism'] }}</p>
            <div class="garis-5"></div>
        </div>
        <div class="form">
            <p>di {{ $data['branch']->name }}</p>
            <div class="garis-6"></div>
        </div>
        <div class="form">
            <div class="kotak">Pas Foto<br/>(2 x 3)</div>
            <div class="garis-7">
                <span class="p">Jakarta, {{$cd[1]}}</span>
                <div class="garis-9"></div>
            </div>
        </div>
        <div class="form-1">
            <p class="name">{{ $data['who_signed'] }}</p>
            <div class="garis-8"></div>
            <p>Gembala Jemaat</p>
        </div>
    </div>
  </div>
</body>

</html>