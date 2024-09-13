<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat SHDR</title>
    <style>
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

        .h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 0;
            text-align: center;
            margin: 0px;
        }

        .h2 {
            font-size: 2.5rem;
            font-weight: 500;
            margin-bottom: 0;
            text-align: center;
            margin: 0px;
        }

        .no {
            font-size: larger;
            text-align: center;
            margin: 2px 0px 0px;
        }

        .under {
            text-decoration: underline;
        }

        hr {
            width: 100%;
            margin: 0px;
        }

        table {
            width: 100%;
        }

        table tr td {
            padding: 10px 0px;
        }

        .item {
            font-size: large;
            text-align: center;
            margin: 20px;
        }

        .garis-10 {
            width: 100%;
            height: 0.3px;
            margin: 0 0 7px 0;
            background-color: black;
        }
    </style>
</head>

<body>
    <div class="a4">
        <div class="a5">
            <div>
                <p class="h1"><i>SERTIFIKAT</i></p>
                <p class="h2">Seminar Hidup dalam Roh</p>
                <table width="75%" style="margin:0px auto; font-weight:bold;" class="no">
                    <tr>
                        <td width="8%">
                            No.
                        </td>
                        <td width="92%" style="text-align:center; margin-bottom:0px;">
                            {{ $data['no']}}
                            <div class="garis-10"></div>
                        </td>
                    </tr>
                </table>
                <p style="text-align: center; font-weight: 700;">Diberikan pada</p>
                <table>
                    <tr>
                        <td width="20%">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Nama</span>

                            </div>
                        </td>
                        <td><span>:</span></td>
                        <td width="78%">
                            <span>{{$data['user']->name}}</span>
                            <div class="garis-10"></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="20%">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Umur</span>

                            </div>
                        </td>
                        <td><span>:</span></td>
                        <td width="78%">
                            <span>{{ $age }} Tahun</span>
                            <div class="garis-10"></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="20%" style="padding-top:0;">
                            <span>Alamat</span>
                        </td>
                        <td style="padding-top:0;"><span class="margin-top:0px; padding-top:0px;">:</span></td>
                        <td width="78%" style="padding-top:0;">
                            <span>{{$data['user']->address}}</span>
                            <div class="garis-10"></div>
                        </td>
                    </tr>
                </table>
                <p style="text-align: center; font-weight: 700; margin:20px 0px 0px 0px;">Sebagai tanda telah mengikuti</p>
                <p style="text-align: center; font-weight: 700; font-size:1.4rem;margin:5px">Seminar Hidup Dalam Roh</p>
                <table>
                    <tr>
                        <td width="10%">Tanggal</td>
                        <td width="44%" style="text-align:center;">
                            {{$date[1]}}
                            <div class="garis-10"></div>
                        </td>
                        <td width="2%">s/d</td>
                        <td width="44%" style="text-align:center;">
                            {{$dateUntil[1]}}
                            <div class="garis-10"></div>
                        </td>
                    </tr>
                </table>
                <table>
                    <tr>
                        <td style="width:50%"></td>
                        <td style="text-align:center">
                            <span>Jakarta, {{ $dateUntil[1] }}</span>
                            <div class="garis-10"></div>
                        </td>
                    </tr>
                </table>
                <div style="position: relative;">
                    <table style="margin-top: 100px;">
                        <tr>
                            <td style="width:40%;text-align:center">
                                <span>Hendaklah kamu penuh dengan Roh (Efesus 5:18b)</span>
                                <div class="garis-10"></div>
                            </td>
                            <td style="width:10%"></td>
                            <td style="width:50%; text-align:center; vertical-align: bottom;">
                                <span>{{$data['who_signed']}}</span>
                                <div class="garis-10"></div>
                            </td>
                        </tr>
                    </table>
                    <div style="position: absolute; right:0; width:260px; text-align:center; top:50px">
                        <p style="font-size: 0.8rem; margin:0px;"><b>Gembala Jemaat</b></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>