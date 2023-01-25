@extends('layouts.chat')

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-12 text-white bg-dark p-4" id="chat-window" style="min-height: 500px"></div>
        </div>
        <div class="row mt-5 justify-content-center">
            <div class="col-6">
                <div id="form-group" class="form-group">
                    <label for="message-input" class="form-label fz-120p">Input message</label>
                    <textarea id="messageControl" name="message" class="form-control"
                              cols="30" rows="10" id="message-input"
                    ></textarea>
                </div>
                <button type="submit" onclick="submit()" class="btn btn-dark btn-lg">Submit</button>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const messages = [];
        let isUserTyping = true;
        let typingTimer;
        const typingMessage = document.createElement('SMALL');
        typingMessage.setAttribute('id', 'typing-message');
        typingMessage.classList.add('form-text', 'text-muted');
        typingMessage.innerText = 'Somebody typing...';

        // WHISPER ONLY FOR PRIVATE EVENTS
        // const onPrint = (event) => {
        //     if (event.target.value !== '' || event.target.value !== undefined) {
        //         console.log('someone starts type');
        //         window.Echo.channel('chat').whisper('typing', true);
        //     }
        // }
        // document.getElementById('messageControl').addEventListener('keyup', onPrint);

        const pushMessage = (text) => {
            messages.push(text);
            const p = document.createElement('P');
            p.innerText = text;
            document.getElementById('chat-window').append(p);
        }

        const clearInput = () => {
            document.getElementById('messageControl').value = '';
        }

        const onUserTyping = (event) => {
            console.log(event);
            isUserTyping = event;
            if (typingTimer) {
                clearTimeout(typingTimer);
            }
            if (isUserTyping) {
                document.getElementById('form-group').append(typingMessage);
            }
            typingTimer = setTimeout(() => {
                isUserTyping = false;
                if (document.getElementById('typing-message')) {
                    document.getElementById('typing-message').remove();
                }
            }, 2000);
        }

        const submit = () => {
            const message = document.getElementById('messageControl').value;
            console.log(message);
            $.ajax({
                url: '/messages?message=' + message,
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    pushMessage(message);
                    clearInput();
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }

        window.Echo.channel('chat').listen('Message', ({message}) => {
            console.log('here', message);
            pushMessage(message);
        });
        //     .listenForWhisper('typing', () => {
        //     console.log('typing event');
        // });
    </script>
@endsection
