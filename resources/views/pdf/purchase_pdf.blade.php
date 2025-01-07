<!DOCTYPE html>
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <title>Achat _{{$purchase['Ref']}}</title>
      <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
      <style>
            body{
                margin: 0;
                padding: 10 20px;
            }
            table tr.due td {
                border-top: 1px solid;
            }
            table tr {
                height: 25px;
            }
            .footer {
                width: 100%;
                text-align: center;
                position: fixed;
            }
            .footer {
                bottom: 0px;
            }
            table td div{
                padding-bottom:5px
            }
      </style>
   </head>

   <body>
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
        <div style="font-size:32px;font-weight:bold;text-underline-offset: 9px;">
            <u>Bons de réception :</u>
        </div>
         <div class="clearfix" style="padding: 30px 0">
            <div style="float:left">
                <div><strong> Date : </strong>{{$purchase['date']}}</div>
                <div><strong> Numéro : </strong> {{$purchase['Ref']}}</div>
                <div><strong> Statut : </strong> {{$purchase['statut_fr']}}</div>
                <div><strong> État de paiement : </strong> {{$purchase['payment_status_fr']}}</div>
            </div>
            <div style="float:right">
               <table class="table-sm">
                  <tbody>
                     <tr>
                        <td>
                            <div style="font-size:18px;text-decoration:underline;text-underline-offset: 2px;padding-bottom:8px"><strong>Fournisseur :</strong></div>

                           <div><strong>Nom complet :</strong> {{$purchase['supplier_name']}}</div>
                           <div><strong>Téléphone :</strong> {{$purchase['supplier_phone']}}</div>
                           <div><strong>Adresse :</strong>   {{$purchase['supplier_adr']}}</div>
                           <div><strong>Email :</strong>  {{$purchase['supplier_email']}}</div>
                           @if($purchase['supplier_tax'])<div><strong>Numéro de taxe :</strong>  {{$purchase['supplier_tax']}}</div>@endif
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
                     <th>Total TTC</th>
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
                     <td>{{$detail['cost']}} </td>
                     <td>{{$detail['quantity']}}/{{$detail['unit_purchase']}}</td>
                     <td>{{$detail['DiscountNet']}} </td>
                     <td>{{$detail['taxe']}} </td>
                     <td>{{$detail['total']}} </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
         <div  class="clearfix">
             
            <div id="total">
            <table>
               <tr>
                  <td>TVA (DH)</td>
                  <td>{{$purchase['TaxNet']}} </td>
               </tr>
               <tr>
                  <td>Remise</td>
                  <td>{{$purchase['discount']}} </td>
               </tr>
              @if($purchase['shipping'] > 0)
               <tr>
                  <td>Expédition</td>
                  <td>{{$purchase['shipping']}} </td>
               </tr>
               @endif
               <tr>
                  <td>Total</td>
                  <td>{{$symbol}} {{$purchase['GrandTotal']}} </td>
               </tr>

               <tr>
                  <td>Montant payé</td>
                  <td>{{$symbol}} {{$purchase['paid_amount']}} </td>
               </tr>

               <tr>
                  <td>Reste</td>
                  <td>{{$symbol}} {{$purchase['due']}} </td>
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
