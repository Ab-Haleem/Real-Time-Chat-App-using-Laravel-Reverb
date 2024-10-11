<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <ul id="messages" x-init="
                        Echo.channel('chat')
                            .listen('MessageEvent', (event) => {

                                const userName = event.user.name;
                                const messageContent = event.message.content;

                                const newMessage = document.createElement('li');
                                newMessage.innerHTML = `<strong>${userName}:</strong> ${messageContent}`;
                                newMessage.classList.add(currentUserId === event.message.sender_id ? 'sender' : 'receiver');
                                document.getElementById('messages').appendChild(newMessage);

                                document.getElementById('messages').scrollTop = document.getElementById('messages').scrollHeight;
                            });
                    " style="flex-grow: 1; overflow-y: auto; list-style-type: none; padding: 0; margin: 0;">
                        @foreach ($messages as $message)
                            <li class="{{ Auth::id() === $message->user_id ? 'sender' : 'receiver' }}">
                                <strong>{{ $message->user->name }}:</strong> {{ $message->message }}
                            </li>
                        @endforeach
                    </ul>
                    <form id="chatForm">
                        @csrf
                        <div class="flex items-center space-x-2">
                            <input type="text" name="message" id="message" class="mt-3 flex-grow px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-100" placeholder="Enter your message here"/>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded-md shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-indigo-500 dark:hover:bg-indigo-600">
                                Send
                            </button>
                        </div>
                    </form>
                    <p id="error-message" style="color: red"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const currentUserId = {{ Auth::id() }};

        document.getElementById('chatForm').addEventListener('submit', function (event) {
            event.preventDefault();

            let messageInput = document.getElementById('message');
            let message = messageInput.value;
            let errorMessage = document.getElementById('error-message');

            // Clear previous error message
            errorMessage.innerHTML = '';

            if (message.trim() === '') {
                errorMessage.innerHTML = 'Message is empty';
                return;
            }

            // Perform AJAX request
            fetch('{{ route('chat.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: message })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = ''; // Clear input
                    } else {
                        errorMessage.innerHTML = 'Error sending message';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorMessage.innerHTML = 'An unexpected error occurred';
                });
        });
    </script>
</x-app-layout>
