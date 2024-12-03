<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>
    .legal {
        width: 35.56cm;
        height: 21.59cm;
        /* border: black solid 1px; */
        font-family: Arial;
        /* display: flex;
            justify-content: center;
            align-items: center; */
    }

    .f4 {
        width: 33cm;
        height: 21cm;
        margin: 10px 0.5cm 1px;
        /* border: black solid 1px; */
        position: relative;
    }

    .no {
        width: 100%;
        text-align: left;
    }
    
    .no p {
        text-align: left;
        font-size: 0.9cm;
        font-family: sans-serif;
        font-weight: 900;
        font-style: italic;
        margin-left:26cm;
    }

    .table-content,
    .table-content th,
    .table-content td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .garis-10 {
        width: 100%;
        height: 0.3px;
        margin: 0 0 7px 0;
        background-color: black;
    }

    .footer {
        position: absolute;
        left: 0px;
        right: 0px;
        bottom: 0px;
        display: flex;
        justify-content: space-between;
    }
</style>

<body>
    <div class='legal'>
        <div class='f4'>
            <div class='no'>
                <p>
                    No. KK : {{ $data['no'] }}
                </p>
            </div>
            {{-- Title --}}
            <div style="display: flex;justify-content: space-between;">
                <div style="width: 700px;">
                    <table width="100%">
                        <tr>
                            <td width="100px">
                                Nama KK
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td>
                                {{ ucwords(strtolower($data['users'][0]['user']['name'])) }}
                                <div class="garis-10"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Alamat
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td>
                                {{ ucwords(strtolower($data['address'])) }}
                                <div class="garis-10"></div>
                            </td>
                        </tr>
                    </table>
                    <div style="width: 700px;display:flex;justify-content: space-between;">
                        <div style="width:440px;">
                            <table width="100%">
                                <tr>
                                    <td width="100px">
                                    </td>
                                    <td width="1%">
                                    </td>
                                    <td>
                                        -
                                        <div class="garis-10"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Kelurahan
                                    </td>
                                    <td width="1%">
                                        :
                                    </td>
                                    <td>
                                        -
                                        <div class="garis-10"></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div style="width:250px;">
                            <table width="100%">
                                <tr>
                                    <td width="100px">
                                        Kode Pos

                                    </td>
                                    <td width="1%">
                                        :
                                    </td>
                                    <td>
                                        -
                                        <div class="garis-10"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        RT/RW
                                    </td>
                                    <td width="1%">
                                        :
                                    </td>
                                    <td>
                                        -
                                        <div class="garis-10"></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div style="width: 350px;">
                    <table width="100%">
                        <tr>
                            <td width="30%">
                                Telp. Rumah
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td>
                                -
                                <div class="garis-10"></div>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%">
                                Telp. Kantor
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td>
                                -
                                <div class="garis-10"></div>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%">
                                HP / Lainnya
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td>
                                {{ $data['users'][0]['details']['phone_number'] }}
                                <div class="garis-10"></div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            {{-- table --}}
            <table width="100%" style="margin-top: 1cm;" class='table-content'>
                <tr>
                    <th>
                        No
                    </th>
                    <th style="width:250px">
                        Nama Lengkap
                    </th>
                    <th>
                        L/P
                    </th>
                    <th style="width: 120px">
                        Hub Keluarga
                    </th>
                    <th style="width:100px">
                        Tempat Lahir
                    </th>
                    <th style="width:100px">
                        Tgl Lahir
                    </th>
                    <th>
                        Agama
                    </th>
                    <th style="width:150px">
                        Gereja
                    </th>
                    <th style="width:100px">
                        Baptisan Air
                    </th>
                    <th style="width:100px">
                        SHDR
                    </th>
                    <th>
                        Status/Pekerjaan
                    </th>
                </tr>
                @foreach ($data['users'] as $user)
                    <tr style="font-style: italic">
                        <td>
                            {{ $user['sequence'] }}
                        </td>
                        <td>
                            {{ ucwords(strtolower($user['user']['name'])) }}
                        </td>
                        <td style="text-align: center">
                            {{ collect(str_split($user['details']['gender']))->first() }}
                        </td>
                        <td style="text-align: center">
                            {{ $user['status'] == 'Kepala Keluarga' ? 'KK' : $user['status'] }}
                        </td>
                        <td style="text-align: center">
                            {{ $user['details']['place_of_birth'] }}
                        </td>
                        <td style="text-align: center">
                            {{ $user['details']['date_of_birth'] }}
                        </td>
                        <td style="text-align: center">
                            Kristen
                        </td>
                        <td>
                            {{ $user['details']['branch_name'] }}
                        </td>
                        <td style="text-align: center">
                            {{ $user['details']['baptism_date'] }}
                        </td>
                        <td style="text-align: center">
                            {{ $user['details']['date_shdr'] }}
                        </td>
                        <td style="text-align: center">
                            {{ $user['details']['profession'] }}
                        </td>
                    </tr>
                @endforeach
            </table>
            {{-- footer --}}
            <div class='footer'>
                <div style="width:500px">
                    <p style="font-weight: bold;font-style: italic; margin-bottom: 0px;">Pernikahan</p>
                    <table width="100%">
                        <tr>
                            <td width="80px">
                                Tanggal
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td>
                                {{ $marriageData->date }}
                                <div class="garis-10"></div>
                            </td>
                        </tr>
                        <tr>
                            <td width="80px">
                                Gereja
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td>
                                {{ $marriageData->church }}
                                <div class="garis-10"></div>
                            </td>
                        </tr>
                        <tr>
                            <td width="80px">
                                Di
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td>
                                {{ $marriageData->location }}
                                <div class="garis-10"></div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="width: 600px; font-style: italic;">
                    <div style="text-align: center; margin-bottom: 100px;">
                        <p style="margin:0px;">Mengetahui</p>
                        <p style="margin:0px;">Jakarta, {{ now()->format('d/m/Y') }}</p>
                    </div>
                    <div style="display: flex; justify-content: space-between">
                        <div style="width:280px">
                            @if ($data['branch']['id'] === 1)
                                <p style="margin:0px;text-align: center;font-weight:bold">Pdm. Emanuel Gatot, S.Th,
                                    .M.Ag</p>
                            @elseif($data['branch']['id'] === 2)
                                <p style="margin:0px;text-align: center;font-weight:bold">Pnt. Marsudi Hardono</p>
                            @else
                                <p style="margin:0px;text-align: center;font-weight:bold">Pnt. Budiyanto</p>
                            @endif
                            <hr style="margin:0px;" />
                            <p style="margin:0px;text-align: center;">Gembala Jemaat</p>
                        </div>
                        <div style="width:300px">
                            <p style="margin:0px;text-align: center;font-weight:bold">
                                {{ ucwords(strtolower($data['users'][0]['user']['name'])) }}</p>
                            <hr style="margin:0px;" />
                            <p style="margin:0px;text-align: center;">Kepala Keluarga</p>
                        </div>
                    </div>
                </div>
            </div>
</body>

</html>
