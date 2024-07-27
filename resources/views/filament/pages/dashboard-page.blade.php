<x-filament-panels::page>
    @vite('resources/css/app.css')
    <h1 id="typing-quote" class="text-2xl font-bold py-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 animate-pulse"></h1>
    <h3 id="typing-character" class="text-xl font-semibold py-2 text-gray-700"></h3>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quoteElement = document.getElementById('typing-quote');
            const characterElement = document.getElementById('typing-character');

            async function fetchQuote() {
                const randomPage = Math.floor(Math.random() * 34) + 1;
                const response = await fetch(`https://katanime.vercel.app/api/getbyanime?anime=naruto&page=${randomPage}`);
                const data = await response.json();
                if (data.result && data.result.length > 0) {
                    const randomIndex = Math.floor(Math.random() * data.result.length);
                    return data.result[randomIndex];
                }
                throw new Error('No quotes found');
            }

            function typeText(element, text) {
                let index = 0;
                function typeNextCharacter() {
                    if (index < text.length) {
                        element.textContent += text[index];
                        index++;
                        setTimeout(typeNextCharacter, 50); // Kecepatan mengetik (ms)
                    }
                }
                typeNextCharacter();
            }

            fetchQuote().then(quote => {
                typeText(quoteElement, quote.indo);
                setTimeout(() => {
                    typeText(characterElement, `- ${quote.character}`);
                }, quote.indo.length * 50 + 500); // Mulai mengetik karakter setelah quote selesai
            }).catch(error => {
                console.error('Error fetching quote:', error);
                quoteElement.textContent = 'Failed to fetch quote. Please try again.';
            });
        });
    </script>
    {{-- <div>
        @livewire(\App\Filament\Widgets\UserOverview\UserOverview::class)
    </div> --}}

</x-filament-panels::page>
