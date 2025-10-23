<!DOCTYPE html>
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <title>Vente _{{$sale['Ref']}}</title>
      <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
      <style>
            body{
                margin: 0;
                padding: 20px;
            }
            .footer {
                width: 100%;
                text-align: center;
                position: fixed;
            }
            .footer {
                bottom: 0px;
            }
            .title{
                font-size:32px;
                font-weight:bold;
                text-underline-offset: 9px;
            }

            @page {
                margin: 0cm 0cm;
            }

            body {
                background-size: cover;
                background-repeat: no-repeat;
                padding: 10px 25px 0px 25px;
                height: 100%;
                position: relative;
                margin: 0 auto;
                color: black;
                font-size: 14px;
                font-family: 'dejavu sans', sans-serif;

            }

            .child-div {

                padding: 30px 20px 30px 20px;
                float: left;
                box-sizing: border-box; /* Optional, ensures padding and border are included in width */
            }


            .clearfix:after {
                content: "";
                display: table;
                clear: both;
                flex-direction: row;
            }

            a {
                color: #0087C3;
                text-decoration: none;
            }

            #comp {
                font-size: 15px;
                color: black;
                font-weight: 700;
                text-decoration: underline;
            }


            #total {
                float: right;
            }

            #total>table {
                border-collapse: collapse;
                border-spacing: 0;
                margin-bottom: 20px;
            }

            #total>table td {
                padding: 10px;
                background: #FFFFFF;
                line-height: 10px;
            }

            #total>table th {
                padding: 6px;
                background: darkturquoise;
                border-bottom: 1px solid #FFFFFF;
            }

            #total>table th {
                color: #fff;
                font-size: 12px;
                font-weight: 300;
            }

            #total>table tr:last-child {
                border-top: 2px solid;
            }

            #Title-heading {
                float: right;
                margin-right: 160px;
                margin-top: 40px;
                padding-left: 6px;
                min-height: 100px;
                font-size: 90%;
                font-size: 26px;
                color:#000000;
                font-weight: 900;
                text-decoration: underline;
            }

            #logo {
                float: left;
                margin-top: 10px;
                width: 300px;
            }

            #logo img {
                /*height: 90px;*/
                width: 300px;
            }

            #company {

                float: right;
                float: right;
                padding-left: 6px;
                min-height: 90px;
                font-size: 85%;
                padding: 1.5%;
                margin-top: 10px;
            }

            .name {
                font-size: 22px;
            }

            #client .to {
                color: cornflowerblue;
                font-size: 22px;
            }

            h2.name {
                font-size: 1.4em;
                font-weight: normal;
                margin: 0;
                background: darkturquoise;
            }

            #invoice>table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                margin-bottom: 20px;
            }

            #invoice>table td {
                padding: 14px;
                /*background: #EEEEEE;*/
                line-height: 20px;
            }

            #invoice>table th {
                padding: 6px;
                border-bottom: 1px solid #FFFFFF;
                background: #fff;
                color: #000000;
                font-size: 13px;
                font-weight: bold;
            }

            #client>table {
                width: 100%;
                border-collapse: collapse;
                border-spacing: 0;
                margin-bottom: 20px;
            }

            #client>table td {
                padding: 14px;
                /*background: #EEEEEE;*/
                line-height: 20px;
            }

            #client>table th {
                padding: 6px;
                /*color: #000000;*/
                font-size: 13px;
                font-weight: bold;
            }

            #client {
                padding: 0!important;
                float: left;
                width: 50%;
                min-height: 90px;
                font-size: 85%;
            }

            #invoice {
                margin-right: 0px;
                width: 40%;
                float: right;
                padding-left: 6px;
                min-height: 90px;
                font-size: 85%;
                padding: 0%;
            }

            #invoice h1 {
                color: #0087C3;
                font-size: 16px;
                line-height: 1em;
                font-weight: normal;
                margin: 0 0 10px 0;
            }

            #invoice .to {
                color: cornflowerblue;
            }

            #invoice .date {
                font-size: 1.1em;
                color: #777777;
            }

            #details_inv>table {
                font-size: 13px;
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
                border-spacing: 0;
                margin-bottom: 20px;
            }

            #details_inv>table td {
                padding: 6px;
                text-align: left;
                border-bottom: 1px dashed #000;
            }

            #details_inv>table th {
                padding: 6px;
                text-align: left;
                border: 1px solid #000;
                color: #000;
                font-size: 13px;
                font-weight: bold;
            }

            #details_inv>table td h3 {
                color: #57B223;
                font-size: 1.2em;
                font-weight: normal;
                margin: 0 0 0.2em 0;
            }

            #details_inv>table .no {
                color: #FFFFFF;
                font-size: 1.6em;
                background: #57B223;
            }

            #details_inv>table .Ref {
                text-align: left;
                font-size: 16px!important;
            }

            #details_inv>table .Payment {
                text-align: right;
                font-size: 16px!important;
            }

            #details_inv>table .mode {
                text-align: center;
                font-size: 16px!important;
            }

            #details_inv>table td.unit,
            #details_inv>table td.qty,
            #details_inv>table td.total {
                font-size: 1.2em;
            }

            #details_inv>table tbody tr:last-child td {
                border: none;
            }

            #details_inv>table tfoot td {
                padding: 10px 20px;
                background: #FFFFFF;
                border-bottom: none;
                font-size: 1.2em;
                white-space: nowrap;
                border-top: 1px solid #AAAAAA;
            }

            #details_inv>table tfoot tr:first-child td {
                border-top: none;
            }

            #details_inv>table tfoot tr:last-child td {
                color: #57B223;
                font-size: 1.4em;
                border-top: 1px solid #57B223;
            }

            #details_inv>table tfoot tr td:first-child {
                border: none;
            }

            #thanks {
                font-size: 2em;
                margin-bottom: 50px;
                margin-top: 228px;
            }

            #signature {
                color: #777777;
                width: 100%;
                height: 30px;
                position: absolute;
                bottom: 30;
                padding: 8px 0;
                text-align: center;
            }

            #notices {
                padding-left: 6px;
                border-left: 6px solid #0087C3;
            }

            #notices .notice {
                font-size: 1.2em;
            }

            footer {
                color: #777777;
                width: 100%;
                height: 30px;
                position: absolute;
                bottom: 0;
                border-top: 1px solid #AAAAAA;
                padding: 8px 0;
                text-align: center;
            }

            #paiment {
                border: 2px solid;
                padding: 24px;
                width: 659px;
            }
      </style>
   </head>

   <body>
      <header class="clearfix">
         <div id="logo" style="width: 450px">
            <img style="width: 340px;object-fit: cover;" src="{{asset('/images/Complete_logo.png')}}">
         </div>

         <div id="company" style="font-size:16px">

            <div style="font-size:18px;font-weight:bold;text-underline-offset: 9px;">
                <u>{{$sale['DocType']}} :</u>
            </div>
            <div><strong> Réf : </strong> {{$sale['Ref']}}</div>
            <div><strong> Date : </strong>{{$sale['date']}}</div>
            <!--<div><strong> Statut : </strong> {{$sale['statut']}}</div>-->
            <!--<div><strong> État de paiement : </strong> {{$sale['payment_status']}}</div>-->

         </div>

         </div>
      </header>
      <main class="clearfix">
         <div>
             <!-- Header -->
            <div class="clearfix" style="padding: 30px 0">

                <div class="child-div" style="width: 37%;background-color: #e6e6e6;margin-right: 15px">
                    <div style="font-size:15px;text-decoration:underline;text-underline-offset: 2px;padding-bottom:5px"><strong>Émetteur :</strong></div>
                   <div><strong>{{$setting['CompanyName']}}</strong></div>
                   <div>Téléphone : {{$setting['CompanyPhone']}}</div>
                   <div>Email : {{$setting['email']}}</div>
                   <div>Adresse : {{$setting['CompanyAdress']}}</div>
                </div>

                <div class="child-div" style="width: 50%; border: 1px solid #000;">
                    <div style="font-size:15px;text-decoration:underline;text-underline-offset: 2px;padding-bottom:5px"><strong>Addressé à :</strong></div>
                    <div><strong>Client :</strong> {{$sale['client_name']}}</div>
                    <div>Téléphone : {{$sale['client_phone']}}</div>
                    <div>Email : {{$sale['client_email']}}</div>
                    <div>Adresse : {{$sale['client_adr']}}</div>
                    @if($sale['client_tax'])
                    <div>ICE : {{$sale['client_tax']}}</div>
                    @endif
                </div>

             </div>

            <!-- Details -->
            <div id="details_inv">
                <table  class="table-sm" style="border-collapse: collapse;">
                   <thead>
                      <tr>
                         <th>Désignation</th>
                         <th>P.U TTC</th>
                         <th>Qté</th>
                         <th>REMISE</th>
                         <th>TVA (dh)</th>
                         <th>TOTAL TTC</th>
                      </tr>
                   </thead>
                   <tbody>
                      @php($tax=0)
                      @foreach ($details as $detail)
                       @php($tax+=$detail['taxe'] * $detail['quantity'])
                          <tr>
                             <td>
                                <span>{{$detail['code']}} ({{$detail['name']}})</span>
                                   @if($detail['is_imei'] && $detail['imei_number'] !==null)
                                      <p>IMEI/SN : {{$detail['imei_number']}}</p>
                                   @endif
                             </td>
                             <td>{{$detail['price']}} </td>
                             <td>{{$detail['quantity']}}</td>
                             <td>{{$detail['DiscountNet']}} </td>
                             <td>{{$detail['taxe']}} </td>
                             <td>{{$detail['total']}} </td>
                          </tr>
                      @endforeach
                   </tbody>
                </table>
             </div>

            <div class="clearfix">
                <div style="float: left">
                    <p style="font-weight: 500">Arrete {{$sale['DocType']}} a la somme de :</p>
                    <p>{{$sale['GrandTotalText']}}</p>
                </div>
                <div style="float:right">
                    <table>

                        <p style="font-size: 12px">Montants exprimés en Dirham</p>
                       <tr>
                          <td>TVA (DH) :</td>
                          <td style="text-align: right">{{number_format($tax, 2, '.', ' ')}}</td>
                       </tr>
                       @if($sale['discount'] === 0)
                           <tr>
                              <td>Remise :</td>
                              <td style="text-align: right">{{$sale['discount']}} </td>
                           </tr>
                       @endif
                       @if($sale['shipping'] > 0)
                       <tr>
                          <td>Expédition :</td>
                          <td style="text-align: right">{{$sale['shipping']}} </td>
                       </tr>
                       @endif
                       <tr style="background-color: #e6e6e6; padding: 2px">
                          <td>Total :</td>
                          <td style="text-align: right">{{$sale['GrandTotal']}} </td>
                       </tr>

                       <!--<tr>-->
                       <!--   <td>Montant payé</td>-->
                       <!--   <td> : {{$sale['paid_amount']}} </td>-->
                       <!--</tr>-->

                       <!--<tr class="due">-->
                       <!--   <td>Reste</td>-->
                       <!--   <td> : {{$sale['due']}} </td>-->
                       <!--</tr>-->
                    </table>
                 </div>
            </div>
         </div>

         <div class="footer">
             <p style="text-align: center;font-size: 12px;">R.C: 19941 / Patente: 49807399 / I.F: 42789900 / I.C.E: 002397145000044</p>
            <p style="text-align: center;font-size: 12px;">Leaders Negoce FK - 646 Residence Agdal AIT MELLOUL - <a href="mailto:leadersnegosfk@gmail.com">leadersnegosfk@gmail.com</a></p>
         </div>
      </main>
   </body>
</html>
