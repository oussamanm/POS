<!DOCTYPE html>
<html lang="fr">
   <head>
      <meta charset="utf-8">
      <title>Fournisseur : {{$provider['provider_name']}}</title>
      <link rel="stylesheet" href="{{asset('/css/pdf_style.css')}}" media="all" />
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
    </header>
      <main>
        <div id="Title-heading">
            Rapport Fournisseur  : {{$provider['provider_name']}}
        </div>
         <div id="details" class="clearfix">
            <div id="client">
               <table class="table-sm">
                  <thead>
                     <tr>
                        <th class="desc">Détails du Fournisseur</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td>
                           <div><strong>Nom :</strong> {{$provider['provider_name']}}</div>
                           <div><strong>Téléphone :</strong> {{$provider['phone']}}</div>
                           <div><strong>Total des Achats :</strong> {{$provider['total_purchase']}}</div>
                           <div><strong>Total Montant :</strong> {{$symbol}} {{$provider['total_amount']}}</div>
                           <div><strong>Total Payé :</strong> {{$symbol}} {{$provider['total_paid']}}</div>
                           <div><strong>Total Dû pour les Achats :</strong> {{$symbol}} {{$provider['due']}}</div>
                           <div><strong>Total Dû pour les Retours d'Achats :</strong> {{$symbol}} {{$provider['return_Due']}}</div>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </div>
         </div>
         <div id="details_inv">
            <h3 style="margin-bottom:10px">
                  Tous les Achats (Non payés/Partiels)
            </h3>
            <table  class="table-sm">
               <thead>
                  <tr>
                     <th>DATE</th>
                     <th>RÉFÉRENCE</th>
                     <th>PAYÉ</th>
                     <th>DÛ</th>
                     <th>ÉTAT DE PAIEMENT</th>
                  </tr>
               </thead>
               <tbody>
                  @foreach ($purchases as $purchase)
                  <tr>
                     <td>{{$purchase['date']}} </td>
                     <td>{{$purchase['Ref']}}</td>
                     <td>{{$symbol}} {{$purchase['paid_amount']}} </td>
                     <td>{{$symbol}} {{$purchase['due']}} </td>
                     <td>{{$purchase['payment_status']}} </td>
                  </tr>
                  @endforeach
               </tbody>
            </table>
         </div>
         <div style="flex: 1;display: flex;flex-direction: column;justify-content: end;padding: 0 0 15px 0;">
             <p style="text-align: center;font-size: 12px;">R.C: 19941 / Patente: 49807399 / I.F: 42789900 / I.C.E: 002397145000044</p>
            <p style="text-align: center;font-size: 12px;">Leaders Negoce FK - 646 Residence Agdal AIT MELLOUL - <a href="mailto:leadersnegosfk@gmail.com">leadersnegosfk@gmail.com</a></p>
         </div>
      </main>
   </body>
</html>
