import React from "react";
import { __ } from "@wordpress/i18n";

export default function PricingHeader() {
  return (
    <div className="max-w-2xl mx-auto mb-10 text-center lg:mb-14">
      <h2 className="pb-2 text-3xl font-semibold tracking-tight transition-colors border-b scroll-m-20 first:mt-0">
        {__("Preços", "lnd-master-dev")}
      </h2>
      <p className="mt-1 text-muted-foreground">
        {__("Seja qual for seu status, nossas ofertas evoluem de acordo com suas necessidades.", "lnd-master-dev")}
      </p>
    </div>
  );
}