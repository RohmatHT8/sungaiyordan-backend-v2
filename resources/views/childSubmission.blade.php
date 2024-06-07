<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Penyerahan Anak</title>
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
            font-size: 2.5rem;
            font-weight: 700;
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
            padding: 5px 0px;
            /* border: black solid 1px; */
        }

        .item {
            font-size: large;
            text-align: center;
            margin: 8px 0px 0px 0px;
        }
    </style>
</head>

<body>
    <div class="a4">
        <div class="a5">
            <div>
                <p class="h1"><i>SURAT PENYERAHAN ANAK</i></p>
                <p class="no"><b>No. <span class="under">{{ $data['no'] }}</span></b></p>
                <p style="text-align:justify"><i>Biarkan anak-anak itu datang kepada-Ku, jangan menghalang-halangi mereka, sebab orang-orang yang seperti itulah yang empunya Kerajaan Allah. Setelah dipeluknya anak-anka itu, diletakkanNya tanganNya atas mereka dan diberkatiNya mereka itu. (Markus 10:14-16)</i></p>
                <p class="item">SESUAI DENGAN FIRMAN TUHAN</p>
                <p class="item">Pada hari <u>{{ $cd[0]}} </u> tanggal <u>{{$cd[1]}}</u></p>
                <p class="item">TELAH DISERAHKAN KEPADA ALLAH SEBAGAI MILIK TUHAN <br/> DALAM NAMA TUHAN YESUS KRISTUS</p>
                <table style="margin-top:20px; margin-bottom:40px;">
                    <tr>
                        <td style="width:20%;">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Nama</span>
                                <span>:</span>
                            </div>
                        </td>
                        <td>
                            <span>{{ $data['user']->name }}</span>
                            <hr />
                        </td>
                    </tr>
                    <tr>
                        <td style="width:20%">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Lahir di</span>
                                <span>:</span>
                            </div>
                        </td>
                        <td>
                            <span>{{ $data['user']->place_of_birth }}, {{ $db[1] }}</span>
                            <hr />
                        </td>
                    </tr>
                    <tr>
                        <td style="width:20%">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Nama Ayah</span>
                                <span>:</span>
                            </div>
                        </td>
                        <td>
                            <span>{{ $data['user']->father }}</span>
                            <hr />
                        </td>
                    </tr>
                    <tr>
                        <td style="width:20%">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Nama Ibu</span>
                                <span>:</span>
                            </div>
                        </td>
                        <td>
                            <span>{{ $data['user']->mother }}</span>
                            <hr />
                        </td>
                    </tr>
                </table>
                <table style="margin-top: 0px;">
                    <tr>
                        <td style="width:40%;text-align:center">
                        </td>
                        <td style="width:10%"></td>
                        <td style="width:50%; text-align:center; vertical-align: bottom;">
                            <span>Jakarta, {{ $cd[1] }}</span>
                            <hr />
                        </td>
                    </tr>
                </table>
                <div style="position: relative;">
                    <table style="margin-top: 100px;">
                        <tr>
                            <td style="width:40%;text-align:center">
                            </td>
                            <td style="width:10%"></td>
                            <td style="width:50%; text-align:center; vertical-align: bottom;">
                                <span>{{ $data['who_signed'] }}</span>
                                <hr />
                            </td>
                        </tr>
                    </table>
                    <div style="position: absolute; right:0; width:260px; text-align:center; top:30px">
                        <p style="font-size: 0.8rem; margin:0px;"><b>Gembala Jemaat</b></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>