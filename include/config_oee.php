                            <div id="modal-form-config-oee" class="modal fade" aria-hidden="true">
                                <div class="modal-dialog" style="max-width: 500px;">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-sm-12"><h3 class="m-t-none m-b">Config OEE</h3>
                                                    <form action="ajax/update_config.php" method="POST">
                                                    <div class="form-group" id="data_2">
                                                        <label for="machine">เครื่องจักร: </label>
                                                         <select class="select2_config form-control" id="machine" name="machine" style="width: 100%;margin-bottom: 1%;" required>
                                                             <?php foreach ($arr_machine as $key => $value) {?>
                                                             <option value="<?php echo $value ?>" <?php if($value == $_POST["machine"]){ echo " selected=\"selected\""; } ?>><?php echo $value ?></option>
                                                             <?php } ?>
                                                         </select>
                                                         <label for="date_plan">วันที่แผน: </label>
                                                         <div class="input-group date" style="width: 100%;margin-bottom: 1%;">
                                                             <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" id="date_plan" name="date_plan" class="form-control pointer" value="<?php echo $date_plan; ?>" readonly required>
                                                         </div>
                                                         <label for="num_hour">จำนวนชั่วโมงที่เดินเครื่อง: </label>
                                                         <input type="Text" name="num_hour" class="form-control" placeholder="จำนวนชั่วโมงที่เดินเครื่อง" style="width: 100%;margin-bottom: 1%;" required>
                                                         <label for="num_hour">หักทานข้าว: </label>
                                                         <input type="Text" name="meal_break" class="form-control" placeholder="หักทานข้าว" style="width: 100%;margin-bottom: 1%;" required>
                                                         <label for="num_hour">ประชุม (Meeting): </label>
                                                         <input type="Text" name="meeting" class="form-control" placeholder="ประชุม (Meeting)" style="width: 100%;margin-bottom: 1%;" required>
                                                         <label for="num_hour">การทำ 5 ส: </label>
                                                         <input type="Text" name="five_s" class="form-control" placeholder="การทำ 5 ส" style="width: 100%;margin-bottom: 1%;" required>
                                                         <label for="num_hour">จำนวนแผ่นที่เดินงาน ต่อ 1 นาที: </label>
                                                         <input type="Text" name="num_paper_for_day" class="form-control" placeholder="จำนวนแผ่นที่เดินงาน ต่อ 1 นาที" style="width: 100%;margin-bottom: 1%;" required>
                                                         <button type="submit" class="btn btn-primary" style="float:right;" id="btn_update_config"><i class="fa fa-download"></i> Save</button>
                                                     </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>