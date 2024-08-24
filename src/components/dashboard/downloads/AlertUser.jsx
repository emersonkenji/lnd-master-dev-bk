import React, { useContext } from "react";
import { Terminal } from "lucide-react"
import {
  Alert,
  AlertDescription,
  AlertTitle,
} from "@/components/ui/alert"
import { Button } from "@/components/ui/button";
import { AppContext  } from '@/lib/context';

const AlertUser = () => { 
  const { setUserAuthModal } = useContext(AppContext);

  return (
    <Alert className="mt-6 border-orange-600 ">
      <div className="flex justify-start gap-2">
        <Terminal className="w-4 h-4 stroke-orange-600" />
        <AlertTitle className="font-medium text-orange-700">Atenção!</AlertTitle>
      </div>
      <AlertDescription className='flex items-center gap-2 '>
        <p>Para fazer os downloads você precisa estar logado! </p>
        <Button 
          onClick={() => setUserAuthModal(true)}
          className="p-0 bg-transparent shadow-none hover:scale-105 hover:bg-transparent text-lime-600 hover:text-lime-300"
        >
          Entre na sua conta
        </Button>
      </AlertDescription>
    </Alert>
    
  )
}

export default AlertUser



// import React from "react";
// import { Terminal } from "lucide-react"
// import {
//   Alert,
//   AlertDescription,
//   AlertTitle,
// } from "@/components/ui/alert"
// import { Button } from "@/components/ui/button";

//  const AlertUser = () => { 
//   return (
//     <Alert className="mt-6 border-orange-600 ">
//       <div className="flex justify-start gap-2">
//         <Terminal className="w-4 h-4 stronke-orange-600" />
//         <AlertTitle className="font-medium text-orange-700">Atenção!</AlertTitle>
//       </div>
//       <AlertDescription className='flex gap-2'>
//         <p>Para fazer os downloads você precisa estar logado! </p>
//         <Button 
        
//         className="bg-transparent"
//         >Entre na usa conta</Button>
//         {/* <a href="#" className="underline">Entre na usa conta</a> */}
//       </AlertDescription>
//     </Alert>
//   )
// }

// export default AlertUser