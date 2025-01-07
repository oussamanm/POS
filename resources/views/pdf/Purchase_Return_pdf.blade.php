<!DOCTYPE html>
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <title>Retour _{{$return_purchase['Ref']}}</title>
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
      <main>
        <div class="title">
            Retour d'Achat :
        </div>
         <div style="padding: 20px 0" class="clearfix">
            <div style="float:left">
                <div><strong> Date : </strong>{{$return_purchase['date']}}</div>
                <div><strong> Numéro : </strong> {{$return_purchase['Ref']}}</div>
                <div><strong> Ref d'Achat : </strong> {{$return_purchase['purchase_ref']}}</div>
                <div><strong> Statut: </strong> {{$return_purchase['statut']}}</div>
                <div><strong> État de paiement: </strong> {{$return_purchase['payment_status']}}</div>
            </div>
            <div style="float:right">
               <table class="table-sm">
                  <tbody>
                     <tr>
                        <td>
                            <div id="comp"><strong>Fournisseur :</strong></div>
                           <div><strong>Nom complet :</strong> {{$return_purchase['supplier_name']}}</div>
                           <div><strong>Téléphone :</strong> {{$return_purchase['supplier_phone']}}</div>
                           <div><strong>Email :</strong>  {{$return_purchase['supplier_email']}}</div>
                           <div><strong>Adresse :</strong>   {{$return_purchase['supplier_adr']}}</div>
                           @if($return_purchase['supplier_tax'])<div><strong>ICE :</strong>  {{$return_purchase['supplier_tax']}}</div>@endif
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
                     <th>PRODUIT</th>
                     <th>PRIX UNITAIRE</th>
                     <th>QUANTITÉ</th>
                     <th>REMISE</th>
                     <th>TVA (dh)</th>
                     <th>TOTAL</th>
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
         <div class="clearfix"> 
             <div id="total">
                <table>
                   <tr>
                      <td>TVA (dh) :</td>
                      <td>{{$return_purchase['TaxNet']}} </td>
                   </tr>
                   <tr>
                      <td>Remise :</td>
                      <td>{{$return_purchase['discount']}} </td>
                   </tr>
                  @if($return_purchase['shipping'] > 0)
                        <tr>
                          <td>Expédition :</td>
                          <td>{{$return_purchase['shipping']}} </td>
                       </tr>
                   @endif
                   <tr>
                      <td>Total :</td>
                      <td>{{$symbol}} {{$return_purchase['GrandTotal']}} </td>
                   </tr>
                   <tr>
                      <td>Montant payé :</td>
                      <td>{{$symbol}} {{$return_purchase['paid_amount']}} </td>
                   </tr>
                   <tr>
                      <td>Reste :</td>
                      <td>{{$symbol}} {{$return_purchase['due']}} </td>
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
