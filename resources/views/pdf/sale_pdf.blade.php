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