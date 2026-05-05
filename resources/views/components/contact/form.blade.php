          <form class="contact-form" onsubmit="handleSubmit(event)">
            <div class="form-row">
              <div class="form-group">
                <label>Nama Lengkap *</label>
                <input type="text" placeholder="Budi Santoso" required />
              </div>
              <div class="form-group">
                <label>No. WhatsApp / HP *</label>
                <input type="tel" placeholder="+62 812-xxxx-xxxx" required />
              </div>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" placeholder="email@kamu.com" />
            </div>

            <div class="form-group">
              <label>Tipe Pertanyaan</label>
              <select>
                <option value="">— Pilih Topik —</option>
                <option>Informasi Kamar & Harga</option>
                <option>Reservasi / Booking</option>
                <option>Fasilitas & Layanan</option>
                <option>Perubahan / Pembatalan</option>
                <option>Lainnya</option>
              </select>
            </div>

            <div class="form-divider-label">🗓 Detail Menginap (Opsional)</div>
            <div class="form-divider"></div>

            <div class="form-row">
              <div class="form-group">
                <label>Check-in</label>
                <input type="date" />
              </div>
              <div class="form-group">
                <label>Check-out</label>
                <input type="date" />
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Tipe Kamar</label>
                <select>
                  <option value="">— Pilih —</option>
                  <option>Standard Room</option>
                  <option>Deluxe Room</option>
                  <option>Suite Room</option>
                  <option>Family Room</option>
                </select>
              </div>
              <div class="form-group">
                <label>Jumlah Tamu</label>
                <select>
                  <option value="">— Pilih —</option>
                  <option>1 orang</option>
                  <option>2 orang</option>
                  <option>3–4 orang</option>
                  <option>5+ orang</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label>Pesan</label>
              <textarea placeholder="Tulis pertanyaan atau kebutuhan khusus kamu di sini..."></textarea>
            </div>

            <button type="submit" class="btn-submit">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
              Kirim Pesan
            </button>
          </form>

          <div class="form-success" id="form-success">
            <div class="success-icon">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h3>Pesan Terkirim! 🎉</h3>
            <p>Terima kasih sudah menghubungi kami.<br>Tim Berlima akan segera menghubungi kamu dalam 1×24 jam.</p>
