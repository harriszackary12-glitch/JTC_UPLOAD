<!-- Booking form and user bookings list, shown on the main page after login, could be used for order history function -->
<?php
    $product_query = "select pk_product_id, product_name, category, product_description, average_carbon_saving, in_stock from rolsa_products";

    $product_result = $conn->query($product_query);

    if ($product_result->num_rows > 0) {
        $products = [];
        while ($row = $product_result->fetch_assoc()) {
            $products[] = $row;
        }

        $bookings = [];
        $user_id = $_SESSION["user_id"] ?? null;
        if ($user_id) {
            $stmt = $conn->prepare(
                "SELECT b.pk_booking_id, b.created_at, b.scheduled_date, b.booking_type, b.booking_status, 
                        p.pk_product_id, p.product_name
                 FROM rolsa_bookings b
                 LEFT JOIN rolsa_products p ON b.fk_product_id = p.pk_product_id
                 WHERE b.fk_user_id = ?
                 ORDER BY b.scheduled_date ASC, b.created_at DESC"
            );
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($r = $res->fetch_assoc()) {
                    $bookings[] = $r;
                }
                $stmt->close();
            }
        }
        ?>
        <?php if (isset($_SESSION["error"])) { ?>
          <div class="error">
            <h5>Error</h5>
            <p><?php echo htmlspecialchars($_SESSION["error"]); ?></p>
          <?php
        $_SESSION["error"] = null;
        ?>
        </div>
        <?php } ?>
        <div class="row g-4">
          <div class="col-12 col-lg-7">
            <div class="card shadow">
              <div class="card-body login-container">
                <div class="row">
                  <div class="col-md-6">
                    <h5 class="card-title mb-3">Products</h5>

                    <form method="POST" action="/includes/book-consultation.php" id="book-form">
                      <select class="form-select" id="product-select" name="pk_product_id" onchange="showProductDetails(this.value)">
                        <option value="">Select a product...</option>
                        <?php foreach ($products as $product) {
                          $sel = (isset($_POST["pk_product_id"]) && $_POST["pk_product_id"] == $product["pk_product_id"]) ? "selected" : "";
                        ?>
                          <option value="<?php echo $product["pk_product_id"]; ?>" data-instock="<?php echo $product['in_stock'] ? '1' : '0'; ?>" <?php echo $sel; ?>>
                            <?php echo htmlspecialchars($product["product_name"]); ?>
                          </option>
                        <?php } ?>
                      </select>

                      <div id="booking-controls" class="mt-3" style="display:none;">
                        <label for="date-select" class="form-label">Select Date:</label>
                        <input type="date" class="form-control" id="date-select" name="scheduled_date" required>
                        <button type="submit" class="btn btn-primary mt-3" name="action" value="book" id="book-btn">Book Consultation</button>
                      </div>
                    </form>
                  </div>

                  <div class="col-md-6">
                    <div id="product-details" style="display: none;">
                      <?php
                      foreach ($products as $product) { ?>
                        <div id="product-<?php echo $product["pk_product_id"]; ?>" class="product-detail" style="display: none;">
                          <h6 class="mb-2"><strong><?php echo htmlspecialchars($product["product_name"]); ?></strong></h6>
                          <p class="mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($product["category"]); ?></p>
                          <p class="mb-1"><?php echo htmlspecialchars($product["product_description"]); ?></p>
                          <p class="mb-1"><strong>Carbon Saving:</strong> <?php echo htmlspecialchars($product["average_carbon_saving"]); ?></p>
                          <p class="mb-1"><strong>In Stock:</strong> <?php echo ($product["in_stock"] ? "Yes" : "No"); ?></p>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-5">
            <div class="card shadow h-100">
              <div class="card-body login-container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                  <h5 class="card-title mb-0">Your bookings</h5>
                  <div class="toggle-cancelled">
                    <button id="toggle-cancelled" class="btn btn-sm btn-outline-secondary" data-hidden="0">Hide cancelled</button>
                  </div>
                </div>
 
                 <?php if (!$user_id) { ?>
                   <p class="text-muted">Log in to view your bookings.</p>
                 <?php } else if (empty($bookings)) { ?>
                   <p class="text-muted">You have no bookings yet.</p>
                 <?php } else { ?>
                   <ul class="list-group">
                     <?php foreach ($bookings as $b) {
                          $pname = $b["product_name"] ?? "Unknown product";
                          $scheduled = !empty($b["scheduled_date"]) ? htmlspecialchars($b["scheduled_date"]) : "Not scheduled";
                          $created = !empty($b["created_at"]) ? htmlspecialchars($b["created_at"]) : "";
                          $type = htmlspecialchars($b["booking_type"] ?? "Consultation");
                          $status = htmlspecialchars($b["booking_status"] ?? "Pending");
                          $bkid  = (int)$b["pk_booking_id"];
                      ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start booking-item" data-status="<?php echo $status; ?>">
                          <div class="ms-2 me-auto">
                            <div class="fw-bold"><?php echo $pname; ?></div>
                            <small class="text-muted"><?php echo $type; ?> — <?php echo $scheduled; ?></small>
                            <?php if ($created) { ?><div class="small text-muted">Booked on: <?php echo $created; ?></div><?php } ?>
                          </div>

                          <div class="d-flex align-items-center">
                            <span class="badge bg-secondary rounded-pill me-2"><?php echo $status; ?></span>

                            <?php if ($status !== "Cancelled") { ?>
                              <form method="POST" action="/includes/cancel-booking.php" style="margin:0;">
                                <input type="hidden" name="pk_booking_id" value="<?php echo $bkid; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this booking?');">Cancel</button>
                              </form>
                            <?php } else { ?>
                              <button class="btn btn-sm btn-outline-secondary" disabled>Cancelled</button>
                            <?php } ?>
                          </div>
                        </li>
                      <?php } ?>
                   </ul>
                 <?php } ?>
               </div>
             </div>
           </div>
         </div>
 
         <script>
         function showProductDetails(index) {
           if (index === "") {
             document.getElementById("product-details").style.display = "none";
             var bc = document.getElementById("booking-controls");
             if (bc) bc.style.display = "none";
             return;
           }
           document.querySelectorAll(".product-detail").forEach(el => el.style.display = "none");
           var detail = document.getElementById("product-" + index);
           if (detail) detail.style.display = "block";
           document.getElementById("product-details").style.display = "block";
 
           var option = document.querySelector('#product-select option[value="' + index + '"]');
           if (option) {
             var bc = document.getElementById("booking-controls");
             if (bc) bc.style.display = "block";
 
             var instock = option.getAttribute("data-instock") === "1";
             document.getElementById("book-btn").disabled = !instock;
             var outMsg = document.getElementById("out-of-stock");
             if (!instock) {
               if (!outMsg) {
                 outMsg = document.createElement("div");
                 outMsg.id = "out-of-stock";
                 outMsg.className = "text-danger mt-2";
                 outMsg.innerText = "Selected product is out of stock.";
                 bc.appendChild(outMsg);
               }
             } else if (outMsg) {
               outMsg.remove();
             }
           }
         }
 
         (function(){
           var sel = document.getElementById("product-select").value;
           if (sel) showProductDetails(sel);
         })();
 
        (function(){
          var btn = document.getElementById('toggle-cancelled');
          if (!btn) return;
          btn.addEventListener('click', function(){
            var hidden = btn.getAttribute('data-hidden') === '1';
            var items = document.querySelectorAll('.booking-item[data-status="Cancelled"]');
            items.forEach(function(it){
              if (hidden) it.classList.remove('d-none'); else it.classList.add('d-none');
            });
            btn.setAttribute('data-hidden', hidden ? '0' : '1');
            btn.textContent = hidden ? 'Hide cancelled' : 'Show cancelled';
          });
        })();
         </script>
<?php
     } else {
         $error = "Error fetching product data";
     }