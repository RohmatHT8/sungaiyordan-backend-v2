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
            /* font-style: italic; */
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
            width: 12.5cm;
            /* height: 20cm; */
            margin: 220px 2.5cm 0px;
            /* border: black solid 1px; */
            /* padding: 100px 70px; */
            /* text-align: center; */
        }

        .ayat {
            text-align: justify;
            font-size: 4.009mm;
            margin-top: 0px;
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
            margin-left:-30px;
            /* width: 80%; */
        }

        .garis-9 {
            width: 90%;
            height: 0.5px;
            margin: 0;
            background-color: black;
        }

        .garis-8 {
            width: 55%;
            height: 0.5px;
            background-color: black;
            position: absolute;
            top: 113px;
            left: 210px;
        }

        .form-1 p {
            position: absolute;
            top: 100px;
            left: 280px;
        }

        .form-1 .name {
            position: absolute;
            top: 80px;
            left: 190px;
            width: 60%;
            text-align: center;
        }

        .kotak {
            width: 75px;
            height: 100px;
            border: black solid 1.5px;
            position: absolute;
            top: 43px;
            left: 80px;
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
            margin: 5px 0;
        }

        .dalam {
            text-align: center;
        }
        hr {
            background-color:black;
        }
    </style>
</head>

<body>
    <div class="a4">
        <div class="a5">
            <p class="h1">SURAT BAPTIS</p>
            <!-- <p class="no"><b>No. <span class="under">{{ $data['no'] }}</span></b></p> -->
            <table width="75%" style="margin:0px auto;">
                <tr>
                    <td width="8%">
                        No.
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td width="92%" style="text-align:center; margin-bottom:0px;">
                        {{ $data['no']}}
                        <hr style="margin-top:0px;" />
                    </td>

                </tr>
            </table>
            <p class="ayat"><i>Dengan demikian kita telah dikuburkan bersama-sama dengan Dia oleh baptisan dalam kematian, supaya, sama seperti Kristus telah dibangkitkan dari antara orang mati oleh kemuliaan Bapa, demikian juga kita akan hidup dalam hidup yang baru. (Rm. 6:4)</i></p>
            <table width="100%">
                <tr>
                    <td width="14%">
                        Pada hari
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td width="36%" style="text-align:center; margin-bottom:0px;">
                        {{ $cd[0] }}
                        <hr style="margin-top:0px;" />
                    </td>
                    <td width="10%">
                        tanggal
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td width="40%" style="text-align:center; margin-bottom:0px;">
                        {{$cd[1]}}
                        <hr style="margin-top:0px;" />
                    </td>
                </tr>
            </table>
            <!-- <p class="item">Pada hari <u>{{ $cd[0] }}</u> tanggal <u>{{$cd[1]}}</u></p> -->
            <p class="dalam" style="margin-top:1px; font-size:medium;"><b>DALAM NAMA BAPA, ANAK DAN ROH KUDUS <br />DALAM NAMA TUHAN YESUS KRISTUS<br />TELAH DIBAPTISKAN SESUAI DENGAN FIRMAN TUHAN</b></p>
            <table width="100%">
                <tr>
                    <td width="30%">
                        Nama
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td width="1%">
                        :
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td>
                        {{ $data['user']->name}}
                        <hr style="margin-top:0px;" />
                    </td>
                </tr>
                <tr>
                    <td width="30%">
                        Tempat/tanggal
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td width="1%">
                        :
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td>
                        {{ $data['user']->place_of_birth }} / {{ $db[0] }} {{$db[1]}}
                        <hr style="margin-top:0px;" />
                    </td>
                </tr>
                <tr>
                    <td width="30%">
                        Nama Ayah
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td width="1%">
                        :
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td>
                        {{ $data['user']->father }}
                        <hr style="margin-top:0px;" />
                    </td>
                </tr>
                <tr>
                    <td width="30%">
                        Nama Ibu
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td width="1%">
                        :
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td>
                        {{ $data['user']->mother }}
                        <hr style="margin-top:0px;" />
                    </td>
                </tr>
                <tr>
                    <td width="40%">
                        Baptisan ini dilakukan oleh
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td></td>
                    <td width="60%">
                        {{ $data['who_baptism'] }}
                        <hr style="margin-top:0px;" />
                    </td>
                </tr>
            </table>
            <table width="100%">
                <tr>
                    <td width="4%">
                        di
                        <hr style="margin-top:0px; opacity:0;" />
                    </td>
                    <td width="96%" colspan="2">
                        {{ $data['branch']->name }}
                        <hr style="margin-top:0px;" />
                    </td>
                </tr>
            </table>
            <div class="form">
                <div class="kotak">Pas Foto<br />(2 x 3)</div>
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