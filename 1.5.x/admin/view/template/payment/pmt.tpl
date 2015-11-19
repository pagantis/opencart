<?php echo $header; ?>
  <div id="content">
    <div class="breadcrumb">
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
      <?php } ?>
    </div>
    <?php if ($error_warning) { ?>
    <div class="warning"><?php echo $error_warning; ?></div>
    <?php } ?>
    <div class="box">

      <div class="heading">
        <h1><img src="view/image/payment/logo_pmt.png" alt="" /> <?php echo $heading_title; ?></h1>
        <div class="buttons"><a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a>
          <a onclick="location = '<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a></div>
      </div>


              <div class="content">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
                  <table class="form">
                    <tr>
                      <td><span class="required">*</span> <?php echo $test_customer_code; ?></td>
                      <td><input type="text" name="pmt_test_customer_code" value="<?php echo $pmt_test_customer_code; ?>"  id="test_customer_code" placeholder="<?php echo $test_customer_code; ?>"/>
                        <?php if ($error_test_customer_code) { ?>
                        <span class="error"><?php echo $error_test_customer_code; ?></span>
                        <?php } ?></td>
                    </tr>



                    <tr>
                      <td><span class="required">*</span> <?php echo $test_customer_key; ?></td>
                      <td><input type="text" name="pmt_test_customer_key" value="<?php echo $pmt_test_customer_key; ?>"  id="test_customer_key" placeholder="<?php echo $test_customer_key; ?>"/>
                        <?php if ($error_test_customer_key) { ?>
                        <span class="error"><?php echo $error_test_customer_key; ?></span>
                        <?php } ?></td>
                    </tr>



                    <tr>
                      <td> <?php echo $real_customer_code; ?></td>
                      <td><input type="text" name="pmt_real_customer_code" value="<?php echo $pmt_real_customer_code; ?>"  id="real_customer_code" placeholder="<?php echo $real_customer_code; ?>"/>
                        <?php if ($error_real_customer_code) { ?>
                        <span class="error"><?php echo $error_real_customer_code; ?></span>
                        <?php } ?></td>
                    </tr>

                    <tr>
                      <td> <?php echo $real_customer_key; ?></td>
                      <td><input type="text" name="pmt_real_customer_key" value="<?php echo $pmt_real_customer_key; ?>"  id="real_customer_key" placeholder="<?php echo $real_customer_key; ?>"/>
                        <?php if ($error_real_customer_key) { ?>
                        <span class="error"><?php echo $error_real_customer_key; ?></span>
                        <?php } ?></td>
                    </tr>

                  <tr>
                      <td><span class="required">*</span> <?php echo $entry_test; ?></td>

                  <td>
                        <?php if ($pmt_test) { ?>
                    <input type="radio" name="pmt_test" value="1" checked="checked" />
                      <?php echo $text_yes; ?>
                      <?php } else { ?>
                    <input type="radio" name="pmt_test" value="1" />
                      <?php echo $text_yes; ?>
                        <?php } ?>
                        <?php if (!$pmt_test) { ?>
                    <input type="radio" name="pmt_test" value="0" checked="checked" />
                      <?php echo $text_no; ?>
                      <?php } else { ?>
                      <input type="radio" name="pmt_test" value="0" />
                        <?php echo $text_no; ?>
                      <?php } ?>
                    </td>
                  </tr>

                  <tr>
                      <td><?php echo $entry_discount; ?></td>

                  <td>
                  <?php if ($pmt_discount) { ?>
                          <input type="radio" name="pmt_discount" value="1" checked="checked" />
                          <?php echo $text_yes; ?>
                            <?php } else { ?>
                              <input type="radio" name="pmt_discount" value="1" />
                              <?php echo $text_yes; ?>
                                <?php } ?>

                        <?php if (!$pmt_discount) { ?>
                          <input type="radio" name="pmt_discount" value="0" checked="checked" />
                          <?php echo $text_no; ?>
                            <?php } else { ?>
                              <input type="radio" name="pmt_discount" value="0" />
                              <?php echo $text_no; ?>
                                <?php } ?>
                    </td></tr>

                    <tr>
                        <td><?php echo $entry_status; ?></td>

                    <td>


                      <select name="pmt_status" id="input-status" class="form-control">
                        <?php if ($pmt_status) { ?>
                          <option value="1" selected="selected">
                            <?php echo $text_enabled; ?>
                          </option>
                          <option value="0">
                            <?php echo $text_disabled; ?>
                          </option>
                          <?php } else { ?>
                            <option value="1">
                              <?php echo $text_enabled; ?>
                            </option>
                            <option value="0" selected="selected">
                              <?php echo $text_disabled; ?>
                            </option>
                            <?php } ?>
                      </select>
                  </td></tr>


                  <tr>
                    <td><?php echo $entry_sort_order; ?></td>
                    <td>
                      <input type="text" name="pmt_sort_order" value="<?php echo $pmt_sort_order; ?>"  id="input-sort-order" placeholder="<?php echo $entry_sort_order; ?>"/>
                    </td>
                  </tr>



                </table>
                </form>
                </div>
                </div>
                </div>
                <?php echo $footer; ?>
