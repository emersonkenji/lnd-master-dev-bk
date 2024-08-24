import React, {useState} from "react";
import { __ } from "@wordpress/i18n";
import PricingHeader from "@/components/dashboard/price/PricingHeader";
import PricingToggle from "@/components/dashboard/price/PricingToggle";
import PricingCards from "@/components/dashboard/price/PricingCards";
import PricingComparisonTable from "@/components/dashboard/price/PricingComparisonTable";


export default function PricingSection() {
  const [isAnnual, setIsAnnual] = useState(false);

  return (
    <div className="container py-12 lg:py-12">
      <PricingHeader />
      <PricingToggle isAnnual={isAnnual} setIsAnnual={setIsAnnual} />
      <PricingCards isAnnual={isAnnual} />
      <PricingComparisonTable />
    </div>
  );
}