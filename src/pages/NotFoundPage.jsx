import React from 'react';
import { Link, useRouteError } from 'react-router-dom'

const NotFoundPage = () => {
    const error = useRouteError()
    console.error(error)
  return (
    <div className="flex flex-col items-center justify-center h-screen bg-gray-100">
      <div className="max-w-md mx-auto text-center">
        <h1 className="text-9xl font-bold text-lime-600 ">404</h1>
        <p className="mt-4 text-2xl font-semibold text-gray-800">
          Página não encontrada
        </p>
        <p className="mt-2 text-gray-600">
          Desculpe, a página que você está procurando não existe.
          <i>{error.statusText || error.message}</i>
        </p>
        
          <Link to="/"
          className="inline-block mt-6 px-6 py-3 bg-lime-600 text-white font-semibold rounded-md hover:bg-lime-700 transition duration-300"
        >
          Voltar para a página inicial
        </Link>
      </div>
    </div>
  );
};

export default NotFoundPage;