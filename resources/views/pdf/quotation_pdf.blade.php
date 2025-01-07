<!DOCTYPE html>
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <title>Devis :</title>
      <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
      <style>
            body{
                margin: 0;
                padding: 0 20px;
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
      </style>
   </head>

   <body style="min-height: 100vh;margin: 0;padding: 0 20px;display: flex;flex-direction: column;">
      <header class="clearfix">
         <div id="logo">
            <img src="{{asset('/images/Logo-LNFK.png')}}">
         </div>
         <div id="company">
            <div><strong>{{$setting['CompanyName']}}</strong></div>
            <div>Téléphone : {{$setting['CompanyPhone']}}</div>
            <div>Email : {{$setting['email']}}</div>
            <div>Adresse : {{$setting['CompanyAdress']}}</div>
         </div>
         </div>
      </header>
      <main class="clearfix">
         <div class="title">
            Devis :
         </div>
         <div  style="padding: 30px 0" class="clearfix">
            <div style="float:left">
               <div><strong> Date : </strong>{{$quote['date']}}</div>
               <div><strong> Numéro : </strong> {{$quote['Ref']}}</div>
            </div>
            <div style="float:right">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <!--<th class="desc">Client</th>-->
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div id="comp"><strong>Client :</strong></div>
                           <div><strong>Nom complet :</strong> {{$quote['client_name']}}</div>
                           <div><strong>Téléphone :</strong> {{$quote['client_phone']}}</div>
                           <div><strong>Email :</strong>  {{$quote['client_email']}}</div>
                           <div><strong>Adresse :</strong>   {{$quote['client_adr']}}</div>
                           @if($quote['client_tax'])<div><strong>ICE :</strong>  {{$quote['client_tax']}}</div>@endif
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
         <div id="details_inv">
            <table class="table-sm">
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
                  @foreach ($details as $detail)
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
             <div id="total">
                <table>
                   <tr>
                      <td>TVA (dh) :</td>
                      <td>{{$quote['TaxNet']}} </td>
                   </tr>
                   <tr>
                      <td>Remise :</td>
                      <td>{{$quote['discount']}} </td>
                   </tr>
                  @if($quote['shipping'] > 0)
                   <tr>
                      <td>Expédition :</td>
                      <td>{{$quote['shipping']}} </td>
                   </tr>
                   @endif
                   <tr>
                      <td>Total :</td>
                      <td>{{$symbol}} {{$quote['GrandTotal']}} </td>
                   </tr>
                </table>
             </div>
         </div>
         <div class="footer">
             <p style="text-align: center;font-size: 12px;">R.C: 19941 / Patente: 49807399 / I.F: 42789900 / I.C.E: 002397145000044</p>
            <p style="text-align: center;font-size: 12px;">Leaders Negoce FK - 646 Residence Agdal AIT MELLOUL - <a href="mailto:leadersnegosfk@gmail.com">leadersnegosfk@gmail.com</a></p>
         </div>
      </main>
   </body>
</html>
