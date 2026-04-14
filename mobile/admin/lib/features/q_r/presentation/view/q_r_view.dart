import 'dart:io';
import 'package:flutter/material.dart';
import '/core/components/cupertino_page_scaffold_with_child_component.dart';
import '/core/components/scaffold_with_body_component.dart';
import '/features/q_r/presentation/view/widgets/custom_q_r_view_body.dart';

class QRView extends StatelessWidget {
  const QRView({super.key});

  @override
  Widget build(BuildContext context) {
    if (Platform.isIOS) {
      return const CupertinoPageScaffoldWithChildComponent(
        child: CustomQRViewBody(),
      );
    } else {
      return const ScaffoldWithBodyComponent(body: CustomQRViewBody());
    }
  }
}
