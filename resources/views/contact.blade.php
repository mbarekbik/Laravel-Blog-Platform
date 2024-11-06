<x-main-layout>
    <div class="article">
        <div class="contact_form">
            <div class="leave_message">Zostaw wiadomość!</div>
            <div class="body_form">
                <img src="{{ asset('images/open.png') }}" alt="">
                <form method="POST">
                    <label>Imię</label>
                    <input type="text" name="name">
                    <label>Email</label>
                    <input type="email" name="email">
                    <label>Wiadomość</label>
                    <textarea name="body"></textarea>
                    <input type="submit" value="Wyślij">
                </form>
            </div>
        </div>
    </div>
</x-main-layout>
