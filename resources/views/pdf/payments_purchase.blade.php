<!DOCTYPE html>
<html lang="fr" style="margin: 0;padding: 0;">
   <head>
      <meta charset="utf-8">
      <title>Paiement_{{$payment['Ref']}}</title>
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
         <div id="logo">
            <img src="{{asset('/images/Logo-LNFK.png')}}">
         </div>
         <div id="company">
           <div><strong>{{$setting['CompanyName']}}</strong></div>
           <div>Téléphone : {{$setting['CompanyPhone']}}</div>
           <div>Email : {{$setting['email']}}</div>
           <div>Adresse : {{$setting['CompanyAdress']}}</div>
         </div>
      </header>
      
      <main>
        <div class="title">
            Achat Paiement :
        </div>
         <div id="details" class="clearfix">
            <div id="invoice">
               <div><strong> Date : </strong>{{$payment['date']}}</div>
                <div><strong> Numéro : </strong> {{$payment['Ref']}}</div>
            </div>
            <div id="client">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <!--<th class="desc">Fournisseur :</th>-->
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                            <div id="comp"><strong>Fournisseur :</strong></div>
                           <div><strong>Nom :</strong> {{$payment['supplier_name']}}</div>
                           <div><strong>Téléphone :</strong> {{$payment['supplier_phone']}}</div>
                           <div><strong>Adresse :</strong> {{$payment['supplier_adr']}}</div>
                           <div><strong>Email :</strong> {{$payment['supplier_email']}}</div>
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
                     <th>Achat</th>
                     <th>Payé par</th>
                     <th>Montant</th>
                  </tr>
               </thead>
               <tbody>
                  <tr>
                     <td>{{$payment['purchase_Ref']}}</td>
                     <td>{{$payment['Reglement']}}</td>
                     <td>{{$symbol}} {{$payment['montant']}} </td>
                  </tr>
               </tbody>
            </table>
         </div>
         
         <div class="footer">
             <p style="text-align: center;font-size: 12px;">R.C: 19941 / Patente: 49807399 / I.F: 42789900 / I.C.E: 002397145000044</p>
            <p style="text-align: center;font-size: 12px;">Leaders Negoce FK - 646 Residence Agdal AIT MELLOUL - <a href="mailto:leadersnegosfk@gmail.com">leadersnegosfk@gmail.com</a></p>
         </div>
      </main>
   </body>
</html>
