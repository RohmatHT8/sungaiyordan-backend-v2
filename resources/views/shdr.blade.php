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
            width: 14cm;
            /* height: 20cm; */
            margin: 230px 2.5cm 0px;
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
    </style>
</head>

<body>
    <div class="a4">
        <div class="a5">
            <div>
                <p class="h1"><i>SERTIFIKAT</i></p>
                <p class="h2">Seminar Hidup dalam Roh</p>
                <p class="no"><b>No. <span class="under">{{ $data['no'] }}</span></b></p>
                <p style="text-align: center; font-weight: 700;">Diberikan pada</p>
                <table>
                    <tr>
                        <td style="width:20%;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Nama</span>
                                <span>:</span>
                            </div>
                        </td>
                        <td>
                            <span>{{$data['user']->name}}</span>
                            <hr />
                        </td>
                    </tr>
                    <tr>
                        <td style="width:20%">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Umur</span>
                                <span>:</span>
                            </div>
                        </td>
                        <td>
                            <span>{{ $age }} Tahun</span>
                            <hr />
                        </td>
                    </tr>
                    <tr>
                        <td style="width:20%">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Alamat</span>
                                <span>:</span>
                            </div>
                        </td>
                        <td>
                            <span>{{$data['user']->address}}</span>
                            <hr />
                        </td>
                    </tr>
                </table>
                <p style="text-align: center; font-weight: 700; margin:20px 0px 0px 0px;">Sebagai tanda telah mengikuti</p>
                <p style="text-align: center; font-weight: 700; font-size:1.4rem;margin:5px">Seminar Hidup Dalam Roh</p>
                <p class="item">Tanggal <u>{{$date[1]}}</u> s/d <u>{{$dateUntil[1]}}</u></p>
                <table>
                    <tr>
                        <td style="width:50%"></td>
                        <td style="text-align:center">
                            <span>Jakarta, {{ $dateUntil[1] }}</span>
                            <hr />
                        </td>
                    </tr>
                </table>
                <div style="position: relative;">
                    <table style="margin-top: 100px;">
                        <tr>
                            <td style="width:40%;text-align:center">
                                <span>Hendaklah kamu penuh dengan Roh (Efesus 5:18b)</span>
                                <hr />
                            </td>
                            <td style="width:10%"></td>
                            <td style="width:50%; text-align:center; vertical-align: bottom;">
                                <span>{{$data['who_signed']}}</span>
                                <hr />
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