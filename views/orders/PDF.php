<html>
<header>
    <style>
        body {
            font-family: Verdana;
            font-size: 12px;
        }

        table {
            width: 100%;
            margin: 10px 0;
            background-color: #ffffff;
            border-collapse: collapse;
        }
        td{
            vertical-align: top;
        }
        .taxes{
            float: left;
            width: 80%;
        }
        .total{
            float: right;
            width: 80%;
        }
        .lines th,
        .lines td,
        .taxes th,
        .taxes td,
        .total th,
        .total td,
        .border{
            border: solid 1px #000000;
            padding: 5px 8px;
            vertical-align: middle;
        }
        #logo{
            padding: 10px 0;
        }
        #billing_address,
        #delivery_address{
            padding: 0 0 10px 0;
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
        .object{
            padding: 10px 0;
        }
    </style>
</header>
<body>
<table class="root">
    <tr>
        <td id="logo" colspan="2">
            <img src="/assets/images/quiltmania.jpg" width="190">
        </td>
    </tr>
    <tr>
        <td id="delivery_address">
            <b>Adresse de livraison</b><br>
            <?php
            echo $order->name_company . '<br>';
            echo $order->last_name . '<br>';
            echo $order->delivery_address_1;
            echo $order->delivery_address_2 ? '<br>' : '';
            echo $order->delivery_address_2;
            echo $order->delivery_address_3 ? '<br>' : '';
            echo $order->delivery_address_3;
            echo '<br>';
            echo $order->delivery_zipcode . ' ' . $order->delivery_city;
            ?>
        </td>
        <td id="billing_address">
            <b>Adresse de facturation</b><br>
            <?php
            echo $order->name_company . '<br>';
            echo $order->last_name . '<br>';
            echo $order->billing_address_1;
            echo $order->billing_address_2 ? '<br>' : '';
            echo $order->billing_address_2;
            echo $order->billing_address_3 ? '<br>' : '';
            echo $order->billing_address_3;
            echo '<br>';
            echo $order->billing_zipcode . ' ' . $order->billing_city;
            ?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="object">
            <strong>Objet : <?php echo $order->libelle; ?></strong>
        </td>
    </tr>
    <tr>
        <?php if($order->modalities !== ""){ ?>
        <td class="border">
            <strong>Mode de reglement</strong><br>
            <?php echo $order->modalities; ?>
        </td>
        <?php } ?>
        <?php if($order->date_limit !== "0000-00-00 00:00:00"){ ?>
        <td class="border">
            <strong>Date de validité</strong><br>
            <?php  echo date('d/m/Y', strtotime($order->date_limit)); ?>
        </td>
        <?php } ?>
    </tr>
    <tr>
        <td colspan="2">
            <table class="lines">
                <thead>
                <tr>
                    <th class="text-left">#</th>
                    <th class="text-left">Désignation</th>
                    <th>Qte</th>
                    <th>P.U. HT</th>
                    <th>Taxe</th>
                    <?php if($showDiscount){ ?>
                        <th>Remise</th>
                    <?php } ?>
                    <th>T. HT</th>
                    <th>T. TTC</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $subtotal_ht = 0;
                $subtotal_ttc = 0;
                if($lines) {
                    foreach ($lines as $line) {
                        if ($line->type == 'subTotal') {
                            ?>
                            <tr>
                                <td colspan="<?php echo $showDiscount ? 6 : 5; ?>" class="text-right">
                                    <b>Sous-Total</b>
                                </td>
                                <td class="text-right"><b><?php echo number_format(floatval($subtotal_ht), 2, ',', ' '); ?></b></td>
                                <td class="text-right"><b><?php echo number_format(floatval($subtotal_ttc), 2, ',', ' '); ?></b></td>
                            </tr>
                            <?php
                            $subtotal_ht = 0;
                            $subtotal_ttc = 0;
                        } elseif ($line->type == 'comment') {
                            ?>
                            <tr>
                                <td class="text-left" colspan="<?php echo $showDiscount ? 8 : 7; ?>">
                                    <?php echo $line->designation_desc; ?>
                                </td>
                            </tr>
                            <?php
                        } else {
                            $subtotal_ht += floatval($line->total_ht);
                            $subtotal_ttc += floatval($line->total_ttc);
                            ?>
                            <tr>
                                <td class="text-left"><?php echo $line->ref; ?></td>
                                <td class="text-left">
                                    <strong><?php echo $line->designation_title; ?></strong><br/>
                                    <?php echo $line->designation_desc; ?>
                                </td>
                                <td class="text-center"><?php echo floatval($line->qty) === round(floatval($line->qty)) ? intval($line->qty) : number_format(floatval($line->qty), 3, ',', ' '); ?></td>
                                <td class="text-right"><?php echo number_format(floatval($line->price_unit), 2, ',', ' '); ?></td>
                                <td class="text-right"><?php echo number_format(floatval($line->value_taxe), 2, ',', ' ') . '%'; ?></td>
                                <?php if($showDiscount){ ?>
                                    <td class="text-right"><?php echo number_format(floatval($line->discount), 2, ',', ' ') . '%'; ?></td>
                                <?php } ?>
                                <td class="text-right"><?php echo number_format(floatval($line->total_ht), 2, ',', ' '); ?></td>
                                <td class="text-right"><?php echo number_format(floatval($line->total_ttc), 2, ',', ' '); ?></td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td class="text-left">
            <table class="taxes">
                <thead>
                <tr>
                    <th>Base TVA</th>
                    <th>Taux TVA</th>
                    <th>MT TVA</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($tvas as $tva) {
                    ?>
                    <tr>
                        <td><?php echo number_format(floatval($tva['ht']), 2, ',', ' '); ?></td>
                        <td class="text-right"><?php echo number_format(floatval($tva['value_taxe']), 2, ',', ' '); ?>%</td>
                        <td class="text-right"><?php echo number_format(floatval($tva['value']), 2, ',', ' '); ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </td>
        <td class="text-right">
                <?php if(floatval($order->total_discount) > 0){ ?>
                <table class="total">
                    <tr>
                        <td class="text-left">
                            Total HT av remise
                        </td>
                        <td class="text-right">
                            <?php echo number_format(floatval($order->total_prediscount_ht), 2, ',', ' '); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-left">
                            Total TTC av remise
                        </td>
                        <td class="text-right">
                            <?php echo number_format(floatval($order->total_prediscount_ttc), 2, ',', ' '); ?>
                        </td>
                    </tr>
                    <?php if(floatval($order->global_discount) > 0){ ?>
                    <tr>
                        <td class="text-left">
                            Remise globable
                        </td>
                        <td class="text-right">
                            <?php echo number_format(floatval($order->global_discount), 2, ',', ' ') . '%'; ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="text-left">
                            Total remises HT
                        </td>
                        <td class="text-right">
                            <?php echo number_format(floatval($order->total_discount), 2, ',', ' ') ? : '0,00'; ?>
                        </td>
                    </tr>
                </table>
                <?php }?>
            <table class="total">
                <tr>
                    <td class="text-left">
                        <strong>Total HT</strong>
                    </td>
                    <td class="text-right">
                        <?php echo number_format(floatval($order->total_ht), 2, ',', ' '); ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-left">
                        Total TVA
                    </td>
                    <td class="text-right">
                        <?php echo number_format(floatval($order->total_tva), 2, ',', ' '); ?>
                    </td>
                </tr>
                <tr>
                    <td class="text-left">
                        <strong>Total TTC</strong>
                    </td>
                    <td class="text-right">
                        <?php echo number_format(floatval($order->total_ttc), 2, ',', ' '); ?>
                    </td>
                </tr>
            </table>
            Prix en euros
        </td>
    </tr>
</table>
</body>
</html>