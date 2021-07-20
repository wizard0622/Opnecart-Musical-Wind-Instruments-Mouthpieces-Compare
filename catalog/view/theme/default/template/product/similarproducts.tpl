<!-- <table id="table-similar-products" style="width:100%;">     -->
            <tr class="ajax-row" style="color:#38B0E3;">
                <td class="Cell1" style="width:20%">
                    Manufacturer
                </td>
  <td class="theader1">
                    Series
                </td>
                <td class="Cell1" style="width:20%">
                    Model
                </td>
                <td class="Cell1" style="width:20%">
                    Diameter
                </td>
                <td class="Cell1" style="width:20%">
                    Cup Depth*
                </td>
                <td class="Cell1" style="width:20%">
                    Bore
                </td>
            </tr>
<?php if(empty($products)) {?>
        <tr><td colspan="6">Not found any match</td>
        </tr>
<?php } else { ?>
  <?php foreach ($products as $s) { ?>
                <tr style='width:100%'>
                    <td class="Cell1">
                        <div class="name"><?php echo $s['man']; ?></div>
                    </td>
					<td class="Cell1">
                        <div class="name"><?php echo $s['sku']; ?></div>
                    </td>
                    <td class="Cell1">
                        <div class="name"><a href="<?php echo $s['link']; ?>"><?php echo $s['model']; ?></a></div>
                    </td>
                    <td class="Cell1">
                        <div class="name"><?php echo $s['diam']; ?></div>
                    </td>

                    <td class="Cell1">
                        <div class="name"><?php echo $s['cup']; ?></div>
                    </td>

                    <td class="Cell1">
                        <div class="name"><?php echo $s['bore']; ?></div>
                    </td>
                </tr>
  <?php } ?>
<?php } ?>
<!-- </table> -->