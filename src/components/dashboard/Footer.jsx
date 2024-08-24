import React from 'react'

export default function Footer() {
  return (
    <footer className="bottom-0 left-0 z-20 w-full p-3 bg-white border-t shadow md:flex md:items-center md:justify-between md:p-6 dark:bg-background">
    <span className="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2023 Desenvolvido por <a href="https://ricol.com.br/" className="underline">Ricol™</a>. Todos direitos reservados.</span>
     
    {/* <ul className="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-500 dark:text-gray-400 sm:mt-0">
      <li><a href="#" className="hover:underline me-4 md:me-6">About</a></li>
      <li><a href="#" className="hover:underline me-4 md:me-6">Privacy Policy</a></li>
      <li><a href="#" className="hover:underline me-4 md:me-6">Licensing</a></li>
      <li><a href="#" className="hover:underline">Contact</a></li>
    </ul> */}

  </footer>
  )
}
