<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-center">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 content-center">Welcome to the BeaverHealth Vulnerable Web Application Dashboard</h1>
                    </div>
                    <br>
                    <p>The BeaverHealth Vulnerable Web Application is a deliberately vulnerable PHP Laravel/MySQL web application designed to educate users about common web security vulnerabilities from the OWASP Top 10. It provides a safe, controlled environment with comprehensive documentation on each vulnerability, including what they are, how to exploit them, and how to fix them to help security researchers, developers, and students learn through hands-on experience.</p>
                    <br>
                    <h2 class="text-xl font-bold">WARNING!</h2>
                    <br>
                    <p>This application is intended for educational and testing purposes only, and we do not take responsibility for any misuse. <em>Do not publically expose this application to the internet on any machine you control, as it contains vulnerabilities that are not intended to be used in a production environment or exposed to the internet.</em> It is recommend using a virtual machine (such as VirtualBox or VMWare) and setting up the virtual machine to NAT networking mode for the best security of your host machine when running the application.</p>
                    <br>
                    <h2 class="text-xl font-bold">Links</h2>
                    <br>
                    <p><a href="https://github.com/BeaverHealth-Vulnerable-Web-App/BeaverHealth-Vulnerable-Web-App" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">GitHub</a></p>
                    <p><a href="https://github.com/BeaverHealth-Vulnerable-Web-App/BeaverHealth-Vulnerable-Web-App/wiki" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Documentation</a></p>
                    <p><a href="https://owasp.org/www-project-top-ten/" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">OWASP Top 10</a></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>