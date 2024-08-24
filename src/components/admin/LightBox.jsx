/**
 * v0 by Vercel.
 * @see https://v0.dev/t/EbgGiJ91j2o
 * Documentation: https://v0.dev/docs#integrating-generated-code-into-your-nextjs-app
 */

import  React, { useState } from "react"
import { Button } from "@/components/ui/button"

export default function LightBox() {
  const [isOpen, setIsOpen] = useState(false)
  const [currentIndex, setCurrentIndex] = useState(0)
  const images = ["https://planos.lojanegociosdigital.com.br/img/astra-addon.jpg"]
  const handlePrevious = () => {
    setCurrentIndex((prevIndex) => (prevIndex === 0 ? images.length - 1 : prevIndex - 1))
  }
  const handleNext = () => {
    setCurrentIndex((prevIndex) => (prevIndex === images.length - 1 ? 0 : prevIndex + 1))
  }
  const handleClose = () => {
    setIsOpen(false)
  }
  return (
    <>
      <div className="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        {images.map((image, index) => (
          <div
            key={index}
            className="relative overflow-hidden rounded-lg cursor-pointer group"
            onClick={() => {
              setCurrentIndex(index)
              setIsOpen(true)
            }}
          >
            <img
              src={image}
              alt={`Image ${index + 1}`}
              width={600}
              height={600}
              className="object-cover w-full transition-all h-60 group-hover:scale-105"
            />
            <div className="absolute inset-0 flex items-center justify-center transition-opacity opacity-0 bg-black/50 group-hover:opacity-100">
              <MicroscopeIcon className="w-8 h-8 text-white" />
            </div>
          </div>
        ))}
      </div>

      {isOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/80">
          <div className="relative w-full max-w-5xl mx-4 sm:mx-6 md:mx-8 lg:mx-12 xl:mx-16">
            <img
              src={images}
              alt={`Image ${currentIndex + 1}`}
              width={1200}
              height={800}
              className="object-contain w-full h-auto"
            />
            <div className="absolute top-4 right-4">
              <Button
                variant="ghost"
                size="sm"
                className="text-white bg-gray-900/50 hover:bg-gray-900/75"
                onClick={handleClose}
              >
                <XIcon className="w-5 h-5" />
              </Button>
            </div>
            <div className="absolute flex justify-between w-full px-4 inset-y-1/2">
              <Button
                variant="ghost"
                size="sm"
                className="text-white bg-gray-900/50 hover:bg-gray-900/75"
                onClick={handlePrevious}
              >
                <ChevronLeftIcon className="w-5 h-5" />
              </Button>
              <Button
                variant="ghost"
                size="sm"
                className="text-white bg-gray-900/50 hover:bg-gray-900/75"
                onClick={handleNext}
              >
                <ChevronRightIcon className="w-5 h-5" />
              </Button>
            </div>
          </div>
        </div>
      )}
    </>
  )
}

function ChevronLeftIcon(props) {
  return (
    <svg
      {...props}
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="m15 18-6-6 6-6" />
    </svg>
  )
}


function ChevronRightIcon(props) {
  return (
    <svg
      {...props}
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="m9 18 6-6-6-6" />
    </svg>
  )
}


function MicroscopeIcon(props) {
  return (
    <svg
      {...props}
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="M6 18h8" />
      <path d="M3 22h18" />
      <path d="M14 22a7 7 0 1 0 0-14h-1" />
      <path d="M9 14h2" />
      <path d="M9 12a2 2 0 0 1-2-2V6h6v4a2 2 0 0 1-2 2Z" />
      <path d="M12 6V3a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v3" />
    </svg>
  )
}


function XIcon(props) {
  return (
    <svg
      {...props}
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="M18 6 6 18" />
      <path d="m6 6 12 12" />
    </svg>
  )
}