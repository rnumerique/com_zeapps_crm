<html>
<header>
    <style>
        body {
            font-family: Verdana;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 25px 0;
            background-color: #ffffff;
        }
        th, td {
            border-bottom: solid 1px #000000;
            text-align: center;
            padding: 5px 8px;
        }
        #logo, #address{
            float: left;
            width: 50%;
            margin-top: 75px;
            margin-bottom: 25px;
        }
        .text-left {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .text-right{
            text-align: right;
        }
        #total{
            float:right;
            width: 50%;
            border: 1px solid #000000;
            border-radius: 15px;
            padding: 10px;
        }
        .separator{
            width: 100%;
            height: 1px;
            background-color: #000000;
            margin: 10px 0;
        }
    </style>
</header>
<body>
<div id="logo">
    <img src="<?php echo $logo; ?>">
</div>
<div id="address">
    <?php
        if($company)
            echo $company->company_name . '<br>';
        if($contact)
            echo $contact->last_name . ' ' . $contact->first_name . '<br>';
        echo $quote->billing_address_1;
        echo $quote->billing_address_2 ? '<br>' : '';
        echo $quote->billing_address_2;
        echo $quote->billing_address_3 ? '<br>' : '';
        echo $quote->billing_address_3;
        echo '<br>';
        echo $quote->billing_zipcode . ' ' . $quote->billing_city;
    ?>
</div>
<div id="object">
    <strong>Objet : <?php echo $quote->libelle; ?></strong>
</div>
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Désignation</th>
            <th>Qte</th>
            <th>P.U. HT</th>
            <th>Taxe</th>
            <th>Remise</th>
            <th>T. HT</th>
            <th>T. TTC</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $subtotal_ht = 0;
    $subtotal_ttc = 0;
    $total_ht = 0;
    $total_ttc = 0;
    if($lines) {
        foreach ($lines as $line) {
            if ($line->num == 'subTotal') {
                ?>
                <tr>
                    <td colspan="6" class="text-right">
                        Sous-Total
                    </td>
                    <td><?php echo number_format(floatval($subtotal_ht), 2, ',', ' '); ?></td>
                    <td><?php echo number_format(floatval($subtotal_ttc), 2, ',', ' '); ?></td>
                </tr>
                <?php
                $subtotal_ht = 0;
                $subtotal_ttc = 0;
            } elseif ($line->num == 'comment') {
                ?>
                <tr>
                    <td class="text-left" colspan="8">
                        <?php echo $line->designation_desc; ?>
                    </td>
                </tr>
                <?php
            } else {
                $t_ht = floatval($line->price_unit) * floatval($line->qty);
                $subtotal_ht += $t_ht;
                $total_ht += $t_ht;
                $t_ttc = floatval($line->price_unit) * floatval($line->qty) * (1 + (floatval($line->taxe) / 100));
                $subtotal_ttc += $t_ttc;
                $total_ttc += $t_ttc;
                ?>
                <tr>
                    <td><?php echo $line->num; ?></td>
                    <td class="text-left">
                        <strong><?php echo $line->designation_title; ?> :</strong><br/>
                        <?php echo $line->designation_desc; ?>
                    </td>
                    <td><?php echo number_format(floatval($line->qty), 3, ',', ' '); ?></td>
                    <td><?php echo number_format(floatval($line->price_unit), 2, ',', ' '); ?></td>
                    <td><?php echo number_format(floatval($line->taxe), 2, ',', ' ') . '%'; ?></td>
                    <td><?php echo number_format(floatval($line->discount), 2, ',', ' ') . '%'; ?></td>
                    <td><?php echo number_format(floatval($t_ht), 2, ',', ' '); ?></td>
                    <td><?php echo number_format(floatval($t_ttc), 2, ',', ' '); ?></td>
                </tr>
                <?php
            }
        }
    }
    ?>
    </tbody>
</table>
<div id="total">
    <div>
        <strong>Total HT av remise</strong>
        <span><?php echo number_format(floatval($total_ht), 2, ',', ' '); ?></span>
    </div>
    <div>
        <strong>Total TTC av remise</strong>
        <span><?php echo number_format(floatval($total_ttc), 2, ',', ' '); ?></span>
    </div>
    <div class="separator"></div>
    <div>
        <strong>Remise Globable</strong>
        <span><?php echo number_format(floatval($quote->global_discount), 2, ',', ' ') . '%'; ?></span>
    </div>
    <div>
        <strong>Remise (avant taxes)</strong>
        <span><?php $discount_prct = number_format(floatval(floatval($quote->global_discount) / 100), 2, ',', ' '); echo ($discount_prct * $total_ht) ? : '0,00'; ?></span>
    </div>
    <div class="separator"></div>
    <div>
        <strong>Total HT</strong>
        <span><?php echo number_format($total_ht - ($discount_prct * $total_ht), 2, ',', ' '); ?></span>
    </div>
    <div>
        <strong>Total TTC</strong>
        <span><?php echo number_format($total_ttc - ($discount_prct * $total_ttc), 2, ',', ' '); ?></span>
    </div>

</div>
</body>
</html>