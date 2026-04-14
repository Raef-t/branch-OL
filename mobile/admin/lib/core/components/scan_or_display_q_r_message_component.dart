import 'package:flutter/material.dart';
import '/core/components/contain_scan_or_display_q_r_message_component.dart';
import '/core/decorations/box_decorations.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';

class ScanOrDisplayQRMessageComponent extends StatelessWidget {
  const ScanOrDisplayQRMessageComponent({super.key});

  @override
  Widget build(BuildContext context) {
    Size size = MediaQuery.sizeOf(context);
    final isRotait = MediaQuery.orientationOf(context) == Orientation.portrait;
    return Positioned(
      bottom: size.height * (isRotait ? 0.1 : 0.175),
      left: 0,
      right: size.width * (isRotait ? 0.27 : 0.14),
      child: Center(
        child: Container(
          padding: OnlyPaddingWithoutChild.top13AndBottom13AndRight17AndLeft28(
            context: context,
          ),
          decoration: BoxDecorations.boxDecorationToScanOrDisplayQRComponent(
            context: context,
          ),
          child: const ContainScanOrDisplayQRMessageComponent(),
        ),
      ),
    );
  }
}
