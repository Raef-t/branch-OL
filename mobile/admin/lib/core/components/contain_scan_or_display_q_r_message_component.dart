import 'package:flutter/material.dart';
import '/core/components/text_with_image_in_scan_or_display_q_r_message_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/pop_go_router_helper.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/sized_boxs/heights.dart';
import '/gen/assets.gen.dart';

class ContainScanOrDisplayQRMessageComponent extends StatelessWidget {
  const ContainScanOrDisplayQRMessageComponent({super.key});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        TextWithImageInScanOrDisplayQRMessageComponent(
          text: 'مسح QR',
          image: Assets.images.scanQrImage.image(),
          onTap: () {
            pushGoRouterHelper(context: context, view: kScanViewRouter);
            popGoRouterHelper(context: context);
          },
        ),
        Heights.height13(context: context),
        TextWithImageInScanOrDisplayQRMessageComponent(
          image: Assets.images.displayQrImage.image(),
          text: 'عرض QR',
          onTap: () {
            pushGoRouterHelper(context: context, view: kQRViewRouter);
            popGoRouterHelper(context: context);
          },
        ),
      ],
    );
  }
}
