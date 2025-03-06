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
                    <div class="flex justify-center gap-4">
                        <img src="{{ Vite::asset('resources/images/oregon-state-beaver.svg') }}" alt="Oregon State Beaver" class="w-32 h-32">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 content-center">Welcome to the BeaverHealth Vulnerable Web Application Dashboard!</h1>
                    </div>
                    <br>
                    <p>BeaverHealth Vulnerable Web Application Dashboard is a PHP Laravel/MySQL web application that is meant to be vulnerable to various common web application security vulnerabilities found in the OWASP Top 10. The aim of the BeaverHealth Vulnerable Web Application Dashboard is to provide a platform for users to learn about and practice various web application security vulnerabilities in a safe and controlled environment with ample documentation to help the user learn the common web application security vulnerabilities. All of the vulnerabilities within the applicaiton that are implemented have in-depth documentation written about what they are, how to exploit them, and how to fix them where all of this information can be found on the wiki page of the project <a href="https://github.com/BeaverHealth-Vulnerable-Web-App/BeaverHealth-Vulnerable-Web-App/wiki" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">here</a>.</p>
                    <br>
                    <h2 class="text-xl font-bold">WARNING!</h2>
                    <br>
                    <p>This website is intended for educational purposes only. We are not responsible for any damage or loss that may occur due to the use of this website. <em>Do not publically expose this application to the internet on any machine you control, as it will be compromised.</em> It is recommend using a virtual machine (such as VirtualBox or VMWare) and setting up the virtual machine to NAT networking mode.</p>
                    <br>
                    <h2 class="text-xl font-bold">Links</h2>
                    <br>
                    <p><a href="https://github.com/BeaverHealth-Vulnerable-Web-App/BeaverHealth-Vulnerable-Web-App" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">GitHub</a></p>
                    <p><a href="https://github.com/BeaverHealth-Vulnerable-Web-App/BeaverHealth-Vulnerable-Web-App/wiki" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Documentation</a></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>