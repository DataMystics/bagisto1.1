<?php $paystackStandard = app('Webkul\paystack\Payment\Standard') ?>

<body data-gr-c-s-loaded="true" cz-shortcut-listen="true">
    You will be redirected to the paystack website in a few seconds.
    

    <form action="{{ $paystackStandard->getpaystackUrl() }}" id="paystack_standard_checkout" method="POST">
        <input value="Click here if you are not redirected within 10 seconds..." type="submit">

        @foreach ($paystackStandard->getFormFields() as $name => $value)

            <input
                type="hidden"
                name="{{ $name }}"
                value="{{ $value }}"
            />

        @endforeach
    </form>

    <script type="text/javascript">
        document.getElementById("paystack_standard_checkout").submit();
    </script>
</body>