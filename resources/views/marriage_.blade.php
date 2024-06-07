<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Peneguhan Nikah</title>
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
            font-size: 2.3rem;
            font-weight: 700;
            margin-bottom: 0;
            text-align: center;
            margin: 0px;
        }

        .no {
            font-size: larger;
            text-align: center;
            margin: 0px;
        }

        .under {
            text-decoration: underline;
        }

        .ayat {
            text-align: justify;
            font-style: italic;
            margin: 2px;
        }

        .item {
            font-size: large;
            text-align: center;
            margin: 5px;
        }

        .ikrar {
            text-align: center;
            margin: 6px;
        }

        table {
            width: 100%;
        }

        table tr td {
            padding: 5px 0px;
            /* border: black solid 1px; */
        }

        hr {
            width: 100%;
            margin: 0px;
        }
        .garis-7 {
            width: 60%;
            position: absolute;
            top: 0px;
            left: 210px;
            text-align: center;
        }
        .kotak {
            width: 150px;
            height: 100px;
            border: black solid 1.5px;
            position: absolute;
            top: 18px;
            left: 30px;
            font-size: small;
            text-align: center;
            align-content: center;
        }
        .form {
            position: relative;
            text-align: start !important;
        }
        .garis-7 .p {
            margin:0px;
            width: 100%;
        }
        .garis-9 {
            width: 100%;
            height: 1.2px;
            margin: 0;
            background-color: black;
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
        .garis-8 {
            width: 60%;
            height: 1.2px;
            background-color: black;
            position: absolute;
            top: 113px;
            left: 210px;
        }
        .form-1 {
            position: relative;
            text-align: start !important;
        }
    </style>
</head>

<body>
    <div class="a4">
        <div class="a5">
            <p class="h1">SURAT PENEGUHAN NIKAH</p>
            <p class="no"><b>No. <span class="under">{{ $data['no'] }}</span></b></p>
            <p class="ayat">Hai isteri-isteri, tunduklah kepada suamimu, sebagaimana seharusnya di dalam Tuhan. Hai suami-suami, kasihilah isterimu dan janganlah berlaku kasar terhadap dia (Kol. 3:18-19)</p>
            <p class="item">Pada hari <u>{{ $cd[0] }}</u> tanggal <u>{{$cd[1]}}</u></p>
            <p class="ikrar"><b>DI HADAPAN SIDANG JEMAAT TUHAN <br />TELAH DILAKUKAN PERNIKAHAN YANG KUDUS DARI</b></p>
            <table>
                <tr>
                    <td style="width: 40%;">Nama</td>
                    <td>
                        <span>{{ $groomName }}</span>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td>Tempat/tanggal Lahir</td>
                    <td><span>{{ $groomPOB }} / {{ $groomDOB[1] }}</span>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td>Anak Laki-laki dari</td>
                    <td><span>{{$groomFather}}</span>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td>dan</td>
                    <td><span>{{$groomMother}}</span>
                        <hr />
                    </td>
                </tr>
            </table>
            <p style="text-align: center;"><b>DENGAN</b></p>
            <table>
                <tr>
                    <td style="width: 40%;">Nama</td>
                    <td>
                        <span>{{ $brideName }}</span>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td>Tempat/tanggal Lahir</td>
                    <td><span>{{ $bridePOB }} / {{ $brideDOB[1] }}</span>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td>Anak Perempuan dari</td>
                    <td><span>{{$brideFather}}</span>
                        <hr />
                    </td>
                </tr>
                <tr>
                    <td>dan</td>
                    <td><span>{{$brideMother}}</span>
                        <hr />
                    </td>
                </tr>
            </table>
            <table style="margin-bottom: 10px;">
                <tr>
                    <td style="width: 45%;">Pemberkatan ini dilakukan oleh</td>
                    <td>
                        <span>{{ $data['who_blessed'] }}</span>
                        <hr />
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
                <p class="name">{{ $shepherd }}</p>
                <div class="garis-8"></div>
                <p>Gembala Jemaat</p>
            </div>
        </div>
    </div>
</body>

</html>