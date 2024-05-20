<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Baptis</title>
    <style>
        .h1 {
            font-weight: bold;
            font-style: italic;
            font-size: 3rem;
            margin-bottom: 0;
        }
        .a4 {
            width: 21cm;
            height: 29.7cm;
            /* border: 1px black solid; */
        }
        .a5 {
            width: 14cm;
            /* border: 1px black solid; */
            text-align: center;
        }
        .ayat {
            text-align: justify;
        }
        .no {
            font-weight: bold;
            font-size: larger;
        }
        .item {
            font-weight: 500;
            font-size: large;
        }
        .under {
            text-decoration: underline;
        }
    </style>
</head>

<body>
  <div class="a4"> 
    <div class="a5">
        <p class="h1">SURAT BAPTIS</p>
        <p class="no">No. <span class="under">{{ $data['no'] }}</span></p>
        <p class="ayat"><i>Dengan demikian kita telah dikuburkan bersama-sama dengan Dia oleh baptisan dalam kematian, supaya, sama seperti Kristus telah dibangkitkan dari antara orang mati oleh kemuliaan Bapa, demikian juga kita akan hidup dalam hidup yang baru. (Rm. 6:4)</i></p>
        <p class="item">Pada hari <u>{{ $cd[0] }}</u> tanggal <u>{{$cd[1]}}</u></p>
        <p><b>DALAM NAMA BAPA, ANAK DAN ROH KUDUS <br/>DALAM NAMA TUHAN YESUS KRISTUS<br/>TELAH DIBAPTISKAN SESUAI DENGAN FIRMAN TUHAN</b></p>
    </div>
  </div>
</body>

</html>